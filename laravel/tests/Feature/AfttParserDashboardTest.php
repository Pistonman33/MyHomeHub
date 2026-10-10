<?php

namespace Tests\Feature;

use App\Services\AfttParser;
use Tests\TestCase;

class AfttParserDashboardTest extends TestCase
{
    public function test_it_extracts_points_and_ranking_from_new_dashboard_markup(): void
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="fr">
<body>
    <div class="stat-card">
        <span>Points de départ</span>
        <strong>1825,5</strong>
    </div>
    <div class="stat-card">
        <span>Points actuels</span>
        <strong>1984,25</strong>
    </div>
    <div class="stat-card">
        <span>Classement Belgique</span>
        <strong>42</strong>
    </div>
</body>
</html>
HTML;

        $parser = new AfttParser();
        $data = $parser->parseDashboard($html);

        $this->assertSame(1825.5, $data['starting_points']);
        $this->assertSame(1984.25, $data['recent_points']);
        $this->assertSame(42, $data['ranking_belgium']);
    }

    public function test_it_extracts_current_season_and_matches_from_redesigned_fiche(): void
    {
        $html = <<<'HTML'
<html lang="fr">
<body>
    <header><h2>Situation dans le classement mixte</h2><span>Saison 2026-2027</span></header>
    <div class="profile-metric"><span class="profile-metric-label">Points actuels</span><span class="profile-metric-value">277,62</span></div>
    <div class="profile-metric"><span class="profile-metric-label">Points de base</span><span class="profile-metric-value">266,57</span></div>
    <div class="card day-result-card w-100">
        <div class="day-result-header">
            <h6>03/10/2026 - PBBWH03/092 - Fonteny Genappe</h6>
            <span class="day-points-badge positive">Total : +12.41 pts</span>
        </div>
        <div class="match-card-modern">
            <div class="opponent-info"><button class="opponent-link">Bernard Cornet</button></div>
            <div class="match-score"><h5>3-1</h5><small>249.581 pts</small></div>
            <span class="match-delta positive">+6.8 pts</span>
        </div>
        <div class="match-card-modern">
            <div class="opponent-info"><button class="opponent-link">Gaelle Fievez</button></div>
            <div class="match-score"><h5>1-3</h5><small>314.35 pts</small></div>
            <span class="match-delta negative">-5,44 pts</span>
        </div>
    </div>
</body>
</html>
HTML;

        $parser = new AfttParser();
        $data = $parser->parseDashboard($html);
        $matches = $parser->parseMatches($html);

        $this->assertSame(2027, $data['season_year']);
        $this->assertSame(266.57, $data['starting_points']);
        $this->assertSame(277.62, $data['recent_points']);
        $this->assertCount(1, $matches);
        $this->assertSame('PBBWH03/092', $matches[0]['match_id']);
        $this->assertCount(2, $matches[0]['players']);
        $this->assertSame('Bernard Cornet', $matches[0]['players'][0]['name']);
        $this->assertSame(249.581, $matches[0]['players'][0]['opponent_points']);
        $this->assertSame(6.8, $matches[0]['players'][0]['delta']);
        $this->assertSame(-5.44, $matches[0]['players'][1]['delta']);
    }
}
