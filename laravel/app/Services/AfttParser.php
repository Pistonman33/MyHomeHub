<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class AfttParser
{
    public function parseDashboard(string $html): array
    {
        $normalized = preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5));

        $seasonYear = null;
        if (preg_match('/Saison\s+(\d{4})\s*[-–]\s*(\d{4})/i', $normalized, $seasonMatches)) {
            $seasonYear = (int) $seasonMatches[2];
        }

        $starting_points = $this->extractNumberByLabel($normalized, [
            'points de départ',
            'points depart',
            'point de départ',
            'point depart',
            'points de base',
            'points de base',
        ]);

        $recent_points = $this->extractNumberByLabel($normalized, [
            'points actuels',
            'points actu',
            'point actuel',
            'points publiés',
            'points publies',
            'total de points',
        ]);

        $ranking = $this->extractNumberByLabel($normalized, [
            'classement belgique',
            'classement belge',
            'rang belgique',
            'rang belge',
            'ranking belgique',
            'ranking',
        ], true);

        return [
            'season_year' => $seasonYear,
            'starting_points' => $starting_points,
            'recent_points' => $recent_points,
            'ranking_belgium' => $ranking,
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

        $cards = $xpath->query(
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' day-result-card ')]"
            . " | //div[contains(concat(' ', normalize-space(@class), ' '), ' card shadow-sm ')]"
        );

        foreach ($cards as $card) {

            $headerNode = $xpath->query(
                ".//div[contains(concat(' ', normalize-space(@class), ' '), ' day-result-header ')]//h6"
                . " | .//div[contains(concat(' ', normalize-space(@class), ' '), ' card-header ')]//h6",
                $card
            )->item(0);
            $headerText = $headerNode ? trim($headerNode->textContent) : null;

            if (!$headerText) {
                continue;
            }

            $date = null;
            $matchId = null;
            $title = null;

            if (preg_match('/(\d{2}\/\d{2}\/\d{4})\s*-\s*([A-Z0-9\/]+)\s*-\s*(.*)/', $headerText, $m)) {
                $date = $m[1];
                $matchId = $m[2];
                $title = trim($m[3]);
            }

            if (!$date || !$matchId) {
                continue;
            }

            $totalNode = $xpath->query(
                ".//span[contains(concat(' ', normalize-space(@class), ' '), ' day-points-badge ')]"
                . " | .//div[contains(concat(' ', normalize-space(@class), ' '), ' card-header ')]//span[contains(concat(' ', normalize-space(@class), ' '), ' badge ')]",
                $card
            )->item(0);
            $totalText = $totalNode ? trim($totalNode->textContent) : null;
            $totalPoints = $this->extractNumber($totalText);

            $players = [];
            $rows = $xpath->query(
                ".//div[contains(concat(' ', normalize-space(@class), ' '), ' match-card-modern ')]"
                . " | .//div[contains(concat(' ', normalize-space(@class), ' '), ' match-card ')]",
                $card
            );

            foreach ($rows as $row) {

                $nameNode = $xpath->query(".//button[contains(@class, 'opponent-link')]", $row)->item(0);
                if (!$nameNode) {
                    $nameNode = $xpath->query(".//button", $row)->item(0);
                }
                $scoreNode = $xpath->query(".//h5", $row)->item(0);
                $pointsNode = $xpath->query(
                    ".//div[contains(concat(' ', normalize-space(@class), ' '), ' match-score ')]//small"
                    . " | .//h5/following::small[1]",
                    $row
                )->item(0);
                $deltaNode = $xpath->query(
                    ".//span[contains(concat(' ', normalize-space(@class), ' '), ' match-delta ')]"
                    . " | .//span[contains(concat(' ', normalize-space(@class), ' '), ' badge ')]",
                    $row
                )->item(0);

                $name = $nameNode ? trim($nameNode->textContent) : null;

                if (!$name) {
                    continue;
                }

                $opponentPoints = $this->extractNumber($pointsNode?->textContent);
                $delta = $this->extractNumber($deltaNode?->textContent);

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

    private function extractNumber(?string $text): ?float
    {
        if ($text === null) {
            return null;
        }

        $text = str_replace('−', '-', $text);
        if (!preg_match('/[-+]?\s*\d+(?:[.,]\d+)?/u', $text, $matches)) {
            return null;
        }

        return (float) str_replace(',', '.', str_replace(' ', '', $matches[0]));
    }


    private function extractNumberByLabel(string $text, array $labels, bool $ranking = false)
    {
        foreach ($labels as $label) {
            $pattern = '/'.preg_quote($label, '/').'[^0-9]{0,60}([0-9]+(?:[.,][0-9]+)?)/iu';
            if (preg_match($pattern, $text, $matches)) {
                $value = str_replace(',', '.', $matches[1]);

                if ($ranking) {
                    $numeric = (int) round((float) $value);
                    if ($numeric < 5000) {
                        return $numeric;
                    }
                    continue;
                }

                return (float) $value;
            }
        }

        if ($ranking) {
            preg_match_all('/(?:rang|classement)[^0-9]{0,60}([0-9]{1,4})/iu', $text, $rankMatches);
            foreach (($rankMatches[1] ?? []) as $raw) {
                $candidate = (int) $raw;
                if ($candidate > 0 && $candidate < 5000) {
                    return $candidate;
                }
            }
        }

        preg_match_all('/([0-9]+(?:[.,][0-9]+)?)/', $text, $matches);
        if ($ranking && ! empty($matches[1])) {
            $candidate = (int) round((float) str_replace(',', '.', $matches[1][0]));
            if ($candidate > 0 && $candidate < 5000) {
                return $candidate;
            }
        }

        return null;
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