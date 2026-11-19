<?php

namespace Tests\Feature;

use App\Livewire\Frontend\Ctt\Dashboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase as BaseTestCase;

class CttOpponentHistoryTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::connection('sqlite')->create('ctt_seasons', function ($table): void {
            $table->integer('year')->primary();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('ctt_matches', function ($table): void {
            $table->id();
            $table->unsignedBigInteger('player_license');
            $table->integer('season_year');
            $table->unsignedBigInteger('opponent_license');
            $table->string('opponent_firstname');
            $table->string('opponent_lastname');
            $table->string('opponent_club')->nullable();
            $table->string('opponent_ranking')->nullable();
            $table->date('date');
            $table->string('result');
            $table->integer('set_for');
            $table->integer('set_against');
            $table->timestamps();
        });
    }

    public function test_it_groups_opponents_and_keeps_encounters_from_all_seasons(): void
    {
        DB::table('ctt_seasons')->insert([
            ['year' => 2025, 'name' => '2024-2025'],
            ['year' => 2026, 'name' => '2025-2026'],
            ['year' => 2027, 'name' => '2026-2027'],
        ]);

        $this->createMatch(1001, 2025, '2025-02-01', 'V', 3, 1);
        $this->createMatch(1001, 2026, '2025-11-15', 'D', 2, 3);
        $this->createMatch(1001, 2027, '2026-10-10', 'V', 3, 0);
        $this->createMatch(1002, 2027, '2026-10-11', 'D', 1, 3, 'Alex');

        $dashboard = new Dashboard;
        $dashboard->player = '167818';
        $dashboard->season = '2027';

        $history = $dashboard->getOpponentHistory();

        $this->assertCount(2, $history);
        $this->assertSame(1001, $history[0]['opponent_license']);
        $this->assertSame('E2', $history[0]['ranking']);
        $this->assertSame(3, $history[0]['total_matches']);
        $this->assertSame(2, $history[0]['wins']);
        $this->assertSame(1, $history[0]['losses']);
        $this->assertSame(
            ['2026-2027', '2025-2026', '2024-2025'],
            $history[0]['matches']->map(fn ($match) => $match->season->name)->all()
        );
        $this->assertSame([3, 2, 3], $history[0]['matches']->map(fn ($match) => $match->set_for)->all());
    }

    private function createMatch(
        int $opponentLicense,
        int $seasonYear,
        string $date,
        string $result,
        int $setFor,
        int $setAgainst,
        string $firstname = 'Sam'
    ): void {
        DB::table('ctt_matches')->insert([
            'player_license' => 167818,
            'season_year' => $seasonYear,
            'opponent_license' => $opponentLicense,
            'opponent_firstname' => $firstname,
            'opponent_lastname' => 'Player',
            'opponent_club' => 'CTT',
            'opponent_ranking' => 'E2',
            'date' => $date,
            'result' => $result,
            'set_for' => $setFor,
            'set_against' => $setAgainst,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
