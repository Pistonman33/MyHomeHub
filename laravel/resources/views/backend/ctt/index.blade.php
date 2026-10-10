@extends('backend.layouts.html')

@section('content')
    @php
        $publicCttUrl = 'https://' . trim((string) env('MYHOME_SUBDOMAIN'), '/') . '/ctt';
    @endphp

    <section class="admin-page admin-ctt-page">
        <div class="admin-page-header">
            <div>
                <span class="admin-eyebrow">MySport</span>
                <h1 class="admin-page-title">CTT</h1>
                <p class="admin-page-subtitle">Synchronize a player season and parse the latest AFTT results.</p>
            </div>
            <a href="{{ $publicCttUrl }}" target="_blank" rel="noopener noreferrer" class="admin-primary-action">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Open public CTT</span>
            </a>
        </div>

        <div class="admin-panel">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label" for="license">License</label>
                    <select id="license" name="license" class="form-select">
                        <option value="">Select a player</option>
                        @foreach ($players as $player)
                            <option value="{{ $player->license }}"
                                {{ (int) $selectedLicense === (int) $player->license ? 'selected' : '' }}>
                                {{ $player->license }} - {{ $player->firstname }} {{ $player->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="season_year">Season year</label>
                    <input id="season_year" name="season_year" type="number" min="2020" step="1"
                        value="{{ $seasonYear }}" class="form-control">
                </div>
            </div>

            @if ($players->isEmpty())
                <div class="alert alert-warning mb-0">No CTT players are configured yet.</div>
            @else
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Sync season</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.ctt.sync') }}">
                                    @csrf
                                    <input type="hidden" name="season_year" value="{{ $seasonYear }}"
                                        data-ctt-season-input>
                                    <input type="hidden" name="license" value="{{ $selectedLicense }}"
                                        data-ctt-license-input>
                                    <p class="mb-3">Run the full CTT season sync for the selected player and season.</p>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-cloud-arrow-down"></i>
                                        Run: php artisan ctt:sync <span data-ctt-sync-label>{{ $selectedLicense }}
                                            {{ $seasonYear }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Parse AFTT results</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.ctt.parse') }}">
                                    @csrf
                                    <input type="hidden" name="season_year" value="{{ $seasonYear }}"
                                        data-ctt-season-input>
                                    <input type="hidden" name="license" value="{{ $selectedLicense }}"
                                        data-ctt-license-input>
                                    <p class="mb-3">Fetch the player's AFTT dashboard and update ranking and points
                                        history.</p>
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrows-rotate"></i>
                                        Run: php artisan ctt:aftt_parse <span
                                            data-ctt-parse-label>{{ $selectedLicense }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if (session('ctt_sync_output'))
            <div class="admin-panel mt-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="admin-section-title mb-0">Sync output</h2>
                    <span class="badge {{ session('ctt_sync_success') ? 'bg-success' : 'bg-danger' }}">
                        {{ session('ctt_sync_success') ? 'Success' : 'Error' }}
                    </span>
                </div>
                <pre class="mb-0 admin-code-output">{{ session('ctt_sync_output') }}</pre>
            </div>
        @endif

        @if (session('ctt_parse_output'))
            <div class="admin-panel mt-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="admin-section-title mb-0">AFTT parse output</h2>
                    <span class="badge {{ session('ctt_parse_success') ? 'bg-success' : 'bg-danger' }}">
                        {{ session('ctt_parse_success') ? 'Success' : 'Error' }}
                    </span>
                </div>
                <pre class="mb-0 admin-code-output">{{ session('ctt_parse_output') }}</pre>
            </div>
        @endif
    </section>

    <script>
        (() => {
            const license = document.getElementById('license');
            const seasonYear = document.getElementById('season_year');

            if (!license || !seasonYear) return;

            const updateCttActions = () => {
                document.querySelectorAll('[data-ctt-license-input]').forEach((input) => {
                    input.value = license.value;
                });
                document.querySelectorAll('[data-ctt-season-input]').forEach((input) => {
                    input.value = seasonYear.value;
                });

                const syncLabel = document.querySelector('[data-ctt-sync-label]');
                const parseLabel = document.querySelector('[data-ctt-parse-label]');
                if (syncLabel) syncLabel.textContent = `${license.value} ${seasonYear.value}`;
                if (parseLabel) parseLabel.textContent = license.value;
            };

            license.addEventListener('change', updateCttActions);
            seasonYear.addEventListener('input', updateCttActions);
            updateCttActions();
        })();
    </script>
@endsection
