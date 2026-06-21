<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use DOMDocument;
use DOMXPath;

class AfttParser
{
    public function parseDashboard(string $html): array
    {
        $crawler = new Crawler($html);

        // 🎯 Points départs
        $starting_points = $crawler
            ->filter('.info-section')
            ->reduce(fn($node) => str_contains($node->text(), 'Départ'))
            ->first()
            ->filter('h3')
            ->text();

        // 🎯 Points actuels
        $recent_points = $crawler
            ->filter('.info-section')
            ->reduce(fn($node) => str_contains($node->text(), 'Actuels'))
            ->first()
            ->filter('h3')
            ->text();

        // 🎯 Ranking
        $ranking = $crawler
            ->filter('.info-section')
            ->reduce(fn($node) => str_contains($node->text(), 'Ranking'))
            ->first()
            ->filter('h3')
            ->text();

        return [
            'starting_points' => $this->cleanPoints($starting_points),
            'recent_points' => $this->cleanPoints($recent_points),
            'ranking_belgium' => $this->cleanRanking($ranking),
        ];
    }

    public function extractMatchPoints(string $html): array
    {
        $crawler = new Crawler($html);

        $matches = [];

        $crawler->filter('.col-lg-4')->each(function ($card) use (&$matches) {

            $headerText = $card->filter('.card-header h6')->text('');

            preg_match('/(\d{2}\/\d{2}\/\d{4})\s-\s([A-Z0-9\/\-]+)/', $headerText, $m);

            $date = $m[1] ?? null;
            $matchId = $m[2] ?? null;

            $totalText = $card->filter('.card-header span')->text('');
            preg_match('/([-+]?\d+(\.\d+)?)/', $totalText, $t);

            $totalPoints = isset($t[0]) ? (float) $t[0] : 0;

            $matches[] = [
                'match_id' => $matchId,
                'date' => $date,
                'total_points' => $totalPoints,
            ];
        });

        return $matches;
    }

    public function parseMatches(string $html): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $matches = [];

        // chaque card match
        $cards = $xpath->query("//div[contains(@class,'card shadow-sm')]");

        foreach ($cards as $card) {

            // HEADER
            $headerNode = $xpath->query(".//div[contains(@class,'card-header')]//h6", $card)->item(0);
            $headerText = $headerNode ? trim($headerNode->textContent) : null;

            if (!$headerText) {
                continue; // ignore faux cards
            }

            // DATE + MATCH_ID (ex: 17/04/2026 - PBBWH21/059 - Set-Jet Fleur Bleue)
            $date = null;
            $matchId = null;
            $title = null;

            if (preg_match('/(\d{2}\/\d{2}\/\d{4})\s*-\s*([A-Z0-9\/]+)\s*-\s*(.*)/', $headerText, $m)) {
                $date = $m[1];
                $matchId = $m[2];
                $title = trim($m[3]);
            }

            // TOTAL POINTS MATCH
            $totalNode = $xpath->query(".//div[contains(@class,'card-header')]//span[contains(@class,'badge')]", $card)->item(0);
            $totalText = $totalNode ? trim($totalNode->textContent) : null;

            $totalPoints = null;
            if ($totalText && preg_match('/([-+]?[0-9]+(?:\.[0-9]+)?)/', $totalText, $m)) {
                $totalPoints = (float) $m[1];
            }

            // PLAYERS
            $players = [];
            $rows = $xpath->query(".//div[contains(@class,'match-card')]", $card);

            foreach ($rows as $row) {

                $nameNode = $xpath->query(".//button", $row)->item(0);
                $scoreNode = $xpath->query(".//h5", $row)->item(0);
                $pointsNode = $xpath->query(".//h5/following::small[1]", $row)->item(0);
                $deltaNode = $xpath->query(".//span[contains(@class,'badge')]", $row)->item(0);

                $name = $nameNode ? trim($nameNode->textContent) : null;

                $opponentPoints = null;
                if ($pointsNode && preg_match('/([0-9]+(?:\.[0-9]+)?)/', $pointsNode->textContent, $m)) {
                    $opponentPoints = (float) $m[1];
                }

                $delta = null;
                if ($deltaNode && preg_match('/([-+]?[0-9]+(?:\.[0-9]+)?)/', $deltaNode->textContent, $m)) {
                    $delta = (float) $m[1];
                }

                $players[] = [
                    'name' => $name,
                    'score' => $scoreNode ? trim($scoreNode->textContent) : null,
                    'opponent_points' => $opponentPoints,
                    'delta' => $delta,
                ];
            }

            $matches[] = [
                'match_id' => $matchId,
                'date' => $date,
                'title' => $title,
                'total_points' => $totalPoints,
                'players' => $players,
            ];
        }

        return $matches;
    }


    private function cleanPoints($value)
    {
        if (!$value) return null;

        // garde uniquement chiffres + point
        return (float) preg_replace('/[^0-9.]/', '', $value);
    }

    private function cleanRanking($value)
    {
        if (!$value) return null;

        return (int) preg_replace('/[^0-9]/', '', $value);
    }
}