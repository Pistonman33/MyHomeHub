<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CttPlayer;
use App\Models\CttSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CttAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $players = CttPlayer::query()
            ->orderBy('license')
            ->get();

        $selectedLicense = (int) ($request->input('license') ?? optional($players->first())->license ?? 0);
        $seasonYear = (int) ($request->input('season_year') ?? CttSeason::currentSeasonYear());

        return view('backend.ctt.index', [
            'players' => $players,
            'selectedLicense' => $selectedLicense,
            'seasonYear' => $seasonYear,
            'syncOutput' => $request->session()->get('ctt_sync_output'),
            'parseOutput' => $request->session()->get('ctt_parse_output'),
            'syncSuccess' => $request->session()->get('ctt_sync_success', false),
            'parseSuccess' => $request->session()->get('ctt_parse_success', false),
        ]);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'license' => ['required', 'integer', 'min:1'],
            'season_year' => ['required', 'integer', 'min:1'],
        ]);

        $exitCode = Artisan::call('ctt:sync', [
            'license' => (int) $validated['license'],
            'year' => (int) $validated['season_year'],
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.ctt.index', [
            'license' => $validated['license'],
            'season_year' => $validated['season_year'],
        ])->with('ctt_sync_output', $output)
            ->with('ctt_sync_success', $exitCode === 0)
            ->with('ctt_parse_output', $request->session()->get('ctt_parse_output'))
            ->with('ctt_parse_success', $request->session()->get('ctt_parse_success', false));
    }

    public function parseResults(Request $request)
    {
        $validated = $request->validate([
            'license' => ['required', 'integer', 'min:1'],
        ]);

        $exitCode = Artisan::call('ctt:aftt_parse', [
            'license' => (int) $validated['license'],
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.ctt.index', [
            'license' => $validated['license'],
            'season_year' => $request->input('season_year', CttSeason::currentSeasonYear()),
        ])->with('ctt_parse_output', $output)
            ->with('ctt_parse_success', $exitCode === 0)
            ->with('ctt_sync_output', $request->session()->get('ctt_sync_output'))
            ->with('ctt_sync_success', $request->session()->get('ctt_sync_success', false));
    }
}
