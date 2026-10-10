<?php

namespace Tests\Feature;

use App\Models\CttSeason;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CttSeasonCurrentYearTest extends TestCase
{
    public function test_season_year_uses_the_following_year_from_september(): void
    {
        Carbon::setTestNow('2026-10-10 12:00:00');
        $this->assertSame(2027, CttSeason::currentSeasonYear());

        Carbon::setTestNow('2026-08-31 12:00:00');
        $this->assertSame(2026, CttSeason::currentSeasonYear());

        Carbon::setTestNow();
    }
}