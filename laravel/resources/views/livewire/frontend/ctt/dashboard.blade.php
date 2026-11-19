<div class="ctt-dashboard p-6 max-w-7xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-3xl font-bold text-white">🏓 CTT Dashboard</h1>

        <select wire:model.live="player"
            class="border rounded-lg px-4 py-2 bg-gray-800 text-white border-gray-700 self-end sm:self-auto">
            @foreach ($players as $p)
                <option value="{{ $p->license }}">
                    {{ $p->firstname }} {{ $p->lastname }}
                </option>
            @endforeach
        </select>
        <select wire:model.live="season"
            class="border rounded-lg px-4 py-2 bg-gray-800 text-white border-gray-700 self-end sm:self-auto">
            <option value="all">Toutes saisons</option>
            @foreach ($seasons as $s)
                <option value="{{ $s->year }}">
                    {{ $s->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- STATS TOP BLOCKS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Classement actuel --}}
        <div class="bg-gray-800/80 text-white rounded-xl shadow-lg p-6 flex flex-col">
            <div class="text-sm opacity-70">Classement actuel</div>

            <div class="flex items-center justify-between mt-4">

                {{-- Ranking principal --}}
                <div class="text-4xl font-bold tracking-wide leading-none">
                    {{ $season_detail->ranking ?? 'N/A' }}
                </div>

                {{-- Ranking Belgique --}}
                <div class="text-right leading-none">
                    <div class="text-3xl font-semibold">
                        {{ $season_detail->ranking_belgium ?? 'N/A' }}e
                    </div>
                    <div class="text-xs text-gray-400">
                        Belgique
                    </div>
                </div>

            </div>
        </div>

        @php
            $delta = $season_detail->current_points - $season_detail->starting_points;
        @endphp

        <div class="bg-gray-800/80 rounded-xl shadow-lg p-6 flex flex-col justify-between">
            <div class="text-sm text-gray-300">Évolution des points</div>

            <div class="mt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Début saison</span>
                    <span class="text-white font-semibold">{{ number_format($season_detail->starting_points ?? 0, 2) }}
                        pts</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Actuel</span>
                    <span class="text-white font-semibold">{{ number_format($season_detail->current_points ?? 0, 2) }}
                        pts</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-400 text-sm">Évolution</span>
                    <span class="font-bold {{ $delta >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 2, ',', ' ') }} pts
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-gray-700/70 rounded-xl shadow-lg p-6 flex flex-col ">
            <div class="text-sm text-gray-300">Performance</div>

            <div class="flex justify-between mt-4">
                <div>
                    <div class="text-green-500 text-3xl font-bold">{{ $stats['wins'] }}</div>
                    <div class="text-xs text-gray-400">Victoires</div>
                </div>

                <div>
                    <div class="text-red-400 text-3xl font-bold">{{ $stats['losses'] }}</div>
                    <div class="text-xs text-gray-400">Défaites</div>
                </div>

                <div>
                    <div class="text-blue-400 text-3xl font-bold">
                        {{ $stats['percentage'] }}%
                    </div>
                    <div class="text-xs text-gray-400">Winrate</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART & PERFORMANCE --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- CHART WIN / LOSS DONUTS --}}
        <div class="bg-gray-800/70 p-6 rounded-xl shadow-lg">
            <h2 class="font-semibold mb-4 text-white">Win / Loss</h2>
            <div class="relative h-72">
                <canvas id="winLossChart"></canvas>
            </div>
        </div>

        {{-- PERFORMANCE PAR CLASSEMENT --}}
        <div class="bg-gray-800/70 p-6 rounded-xl shadow-lg">
            <h2 class="font-semibold mb-4 text-white">Performance par classement</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($rankingStats as $r)
                    @php
                        $total = $r->wins + $r->losses;
                        $winPct = $total ? round(($r->wins / $total) * 100) : 0;
                        $lossPct = $total ? round(($r->losses / $total) * 100) : 0;
                    @endphp

                    <div class="bg-gray-700/40 rounded-xl p-3 shadow-sm flex flex-col gap-2">

                        {{-- Classement en gris --}}
                        <div class="text-sm font-bold text-center py-1 rounded bg-gray-500 text-white">
                            {{ $r->opponent_ranking }}
                        </div>

                        {{-- Victoires / Défaites --}}
                        <div class="flex justify-between text-xs font-semibold">
                            <div class="text-green-500">{{ $r->wins }}</div>
                            <div class="text-red-400">{{ $r->losses }}</div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="w-full h-3 bg-gray-700/40 rounded-full overflow-hidden flex">
                            <div class="h-full bg-green-500" style="width: {{ $winPct }}%"></div>
                            <div class="h-full" style="width: {{ $lossPct }}%; background-color: #ef4444;"></div>
                        </div>

                        {{-- Pourcentage --}}
                        <div class="flex justify-between text-xs font-semibold">
                            <div class="text-green-500">{{ $winPct }}%</div>
                            <div class="text-red-400">{{ $lossPct }}%</div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $opponentHistoryByLicense = $opponentHistory->keyBy('opponent_license');
    @endphp

    {{-- TOP / FLOP adversaires --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        <!-- TOP -->
        <div class="bg-gray-800/70 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">🏆 Top 3 adversaires</h2>
                <span class="text-xs text-gray-400">points gagnés</span>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @foreach ($topOpponents as $opp)
                    <div class="rounded-xl border border-gray-700/70 bg-gray-800/70 px-4 py-3 text-white">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700 text-xs font-bold text-gray-300">
                                    #{{ $loop->iteration }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate text-sm font-semibold text-white">
                                            {{ $opp->opponent_firstname }} {{ $opp->opponent_lastname }}
                                        </span>
                                        @if ($opp->opponent_ranking)
                                            <span
                                                class="flex-shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold
                                                @if ($opp->opponent_ranking < $season_detail->ranking) bg-orange-400/20 text-orange-200 ring-1 ring-inset ring-orange-400/30
                                                @elseif ($opp->opponent_ranking > $season_detail->ranking) bg-teal-400/20 text-teal-200 ring-1 ring-inset ring-teal-400/30
                                                @else bg-gray-500/20 text-gray-300 ring-1 ring-inset ring-gray-500/30 @endif">
                                                {{ $opp->opponent_ranking }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex min-w-0 items-center gap-2 text-xs text-gray-400">
                                        @if ($opp->opponent_club)
                                            <span class="truncate">{{ $opp->opponent_club }}</span>
                                            <span>•</span>
                                        @endif
                                        <span class="flex-shrink-0">{{ $opp->opponent_points ?? 'N/A' }} pts</span>
                                    </div>
                                </div>
                            </div>
                            <span class="flex-shrink-0 text-right text-sm font-bold text-green-400">
                                +{{ number_format($opp->total_delta ?? $opp->delta_points, 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FLOP -->
        <div class="bg-gray-800/70 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">💀 Flop 3 adversaires</h2>
                <span class="text-xs text-gray-400">points perdus</span>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @foreach ($flopOpponents as $opp)
                    <div class="rounded-xl border border-gray-700/70 bg-gray-800/70 px-4 py-3 text-white">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700 text-xs font-bold text-gray-300">
                                    #{{ $loop->iteration }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate text-sm font-semibold text-white">
                                            {{ $opp->opponent_firstname }} {{ $opp->opponent_lastname }}
                                        </span>
                                        @if ($opp->opponent_ranking)
                                            <span
                                                class="flex-shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold
                                                @if ($opp->opponent_ranking < $season_detail->ranking) bg-orange-400/20 text-orange-200 ring-1 ring-inset ring-orange-400/30
                                                @elseif ($opp->opponent_ranking > $season_detail->ranking) bg-teal-400/20 text-teal-200 ring-1 ring-inset ring-teal-400/30
                                                @else bg-gray-500/20 text-gray-300 ring-1 ring-inset ring-gray-500/30 @endif">
                                                {{ $opp->opponent_ranking }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex min-w-0 items-center gap-2 text-xs text-gray-400">
                                        @if ($opp->opponent_club)
                                            <span class="truncate">{{ $opp->opponent_club }}</span>
                                            <span>•</span>
                                        @endif
                                        <span class="flex-shrink-0">{{ $opp->opponent_points ?? 'N/A' }} pts</span>
                                    </div>
                                </div>
                            </div>
                            <span class="flex-shrink-0 text-right text-sm font-bold text-red-400">
                                {{ number_format($opp->total_delta ?? $opp->delta_points, 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    @if ($season === 'all')
    {{-- TOP 10 ALL-TIME HEAD-TO-HEAD OPPONENTS --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Top 12 adversaires rencontrés</h2>
                <p class="text-xs text-gray-400 mt-1">Toutes saisons confondues · survolez un joueur pour voir les rencontres</p>
            </div>
            <span class="text-xs text-gray-500">{{ min(12, $opponentHistory->count()) }} adversaires</span>
        </div>

        @if ($opponentHistory->isEmpty())
            <div class="bg-gray-800/70 rounded-xl p-5 text-sm text-gray-400">
                Aucun historique de rencontre disponible.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($opponentHistory->take(12) as $opponent)
                    @php
                        $tooltipId = 'ctt-opponent-history-' . $loop->index;
                    @endphp
                    <div class="relative group">
                        <button
                            type="button"
                            aria-describedby="{{ $tooltipId }}"
                            class="w-full rounded-xl border border-gray-700/70 bg-gray-800/70 px-4 py-3 text-left text-white transition hover:border-blue-400/50 hover:bg-gray-700/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                        >
                            <span class="flex items-center justify-between gap-3">
                                <span class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-700 text-xs font-bold text-gray-300">
                                        #{{ $loop->iteration }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="flex items-center gap-2">
                                            <span class="truncate text-sm font-semibold">
                                                {{ $opponent['firstname'] }} {{ $opponent['lastname'] }}
                                            </span>
                                            @if ($opponent['ranking'])
                                                <span
                                                    class="flex-shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold
                                                    @if ($opponent['ranking'] < $season_detail->ranking) bg-orange-400/20 text-orange-200 ring-1 ring-inset ring-orange-400/30
                                                    @elseif ($opponent['ranking'] > $season_detail->ranking) bg-teal-400/20 text-teal-200 ring-1 ring-inset ring-teal-400/30
                                                    @else bg-gray-500/20 text-gray-300 ring-1 ring-inset ring-gray-500/30 @endif">
                                                    {{ $opponent['ranking'] }}
                                                </span>
                                            @endif
                                        </span>
                                        @if ($opponent['club'])
                                            <span class="mt-1 block truncate text-xs text-gray-400">{{ $opponent['club'] }}</span>
                                        @endif
                                    </span>
                                </span>
                                <span class="flex-shrink-0 text-right">
                                    <span class="block text-lg font-bold text-blue-300">{{ $opponent['total_matches'] }}</span>
                                    <span class="block text-[10px] uppercase tracking-wide text-gray-400">
                                        {{ $opponent['total_matches'] === 1 ? 'match' : 'matchs' }}
                                    </span>
                                </span>
                            </span>
                            <span class="mt-2 flex gap-3 text-xs">
                                <span class="text-green-400">{{ $opponent['wins'] }} victoire{{ $opponent['wins'] === 1 ? '' : 's' }}</span>
                                <span class="text-red-400">{{ $opponent['losses'] }} défaite{{ $opponent['losses'] === 1 ? '' : 's' }}</span>
                            </span>
                        </button>

                        <div
                            id="{{ $tooltipId }}"
                            role="tooltip"
                            class="invisible absolute left-1/2 top-full z-30 w-72 max-w-[calc(100vw-2rem)] -translate-x-1/2 pt-2 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100"
                        >
                            <div class="overflow-hidden rounded-xl border border-gray-600 bg-gray-900 shadow-2xl">
                                <div class="border-b border-gray-700 px-3 py-2">
                                    <div class="text-xs font-semibold text-white">
                                        {{ $opponent['firstname'] }} {{ $opponent['lastname'] }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $opponent['total_matches'] }} rencontre{{ $opponent['total_matches'] === 1 ? '' : 's' }}
                                        · {{ $opponent['wins'] }} V / {{ $opponent['losses'] }} D
                                    </div>
                                </div>
                                <ol class="max-h-56 divide-y divide-gray-800 overflow-y-auto px-3">
                                    @foreach ($opponent['matches'] as $match)
                                        <li class="flex items-center justify-between gap-3 py-2 text-xs">
                                            <span class="min-w-0">
                                                <span class="block truncate text-gray-200">
                                                    {{ $match->season?->name ?? 'Saison ' . $match->season_year }}
                                                </span>
                                                <span class="text-[10px] text-gray-500">{{ $match->date?->format('d/m/Y') }}</span>
                                            </span>
                                            <span class="flex flex-shrink-0 items-center gap-2">
                                                <span class="rounded px-1.5 py-0.5 font-bold {{ $match->result === 'V' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                                    {{ $match->result === 'V' ? 'Gagné' : 'Perdu' }}
                                                </span>
                                                <span class="w-10 text-right font-semibold text-white">
                                                    {{ $match->set_for }}–{{ $match->set_against }}
                                                </span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    @else
    {{-- LAST MATCHES --}}
    <h2 class="font-semibold mb-4 text-white">Derniers matches</h2>
    <div class="space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
            @foreach ($matchesGrouped as $matches)
                <div class="rounded-xl shadow-sm p-4 flex flex-col gap-3 bg-gray-700/40">

                    {{-- CLUB + DATE + TOTAL POINTS --}}
                    <div class="mb-2 flex justify-between items-start">
                        <div>
                            <h2 class="font-semibold text-lg text-white">
                                {{ $matches->first()->opponent_club }}
                            </h2>
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($matches->first()->date)->format('d M Y') }}
                            </div>
                        </div>

                        @php
                            $totalDelta = $matches->sum(
                                fn($m) => $m->pointsHistoryForPlayer($license)?->delta_points ?? 0,
                            );
                        @endphp

                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $totalDelta >= 0 ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                            {{ $totalDelta > 0 ? '+' : '' }}{{ number_format($totalDelta, 2) }} pts
                        </span>
                    </div>
                    {{-- MATCHES LIST --}}
                    <div class="space-y-2">
                        @foreach ($matches as $match)
                            <div class="ctt-match-row flex justify-between items-center">
                                {{-- OPPONENT + RANKING --}}
                                <div class="ctt-match-opponent flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-md flex items-center justify-center text-sm font-bold
                                        @if ($match->opponent_ranking < $season_detail->ranking) bg-orange-400/20 text-orange-200 ring-1 ring-inset ring-orange-400/30
                                        @elseif ($match->opponent_ranking > $season_detail->ranking) bg-teal-400/20 text-teal-200 ring-1 ring-inset ring-teal-400/30
                                        @else bg-gray-500/20 text-gray-300 ring-1 ring-inset ring-gray-500/30 @endif">
                                        {{ $match->opponent_ranking }}
                                    </div>
                                    <div class="ctt-match-opponent-info">
                                        <div class="flex items-center gap-1.5">
                                            <span class="ctt-match-opponent-name font-medium text-white">
                                                {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}
                                            </span>
                                            @php
                                                $history = $opponentHistoryByLicense->get($match->opponent_license);
                                                $tooltipId = 'ctt-match-history-' . $match->id;
                                            @endphp
                                            @if ($history && $history['total_matches'] > 1)
                                                <span class="ctt-match-history-trigger relative inline-flex group">
                                                    <button
                                                        type="button"
                                                        aria-label="Voir l'historique des rencontres contre {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}"
                                                        aria-describedby="{{ $tooltipId }}"
                                                        aria-expanded="false"
                                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full text-blue-300 transition hover:bg-blue-400/10 hover:text-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M20 12a8 8 0 1 1-2.34-5.66M20 4v4h-4" />
                                                        </svg>
                                                    </button>
                                                    <span
                                                        id="{{ $tooltipId }}"
                                                        role="tooltip"
                                                        class="ctt-match-history-tooltip invisible absolute left-0 top-full z-30 w-72 max-w-[calc(100vw-2rem)] pt-2 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100"
                                                    >
                                                        <span class="block overflow-hidden rounded-xl border border-gray-600 bg-gray-900 text-left shadow-2xl">
                                                            <span class="block border-b border-gray-700 px-3 py-2">
                                                                <span class="block text-xs font-semibold text-white">
                                                                    {{ $history['firstname'] }} {{ $history['lastname'] }}
                                                                </span>
                                                                <span class="block text-[11px] text-gray-400">
                                                                    {{ $history['total_matches'] }} rencontres · {{ $history['wins'] }} V / {{ $history['losses'] }} D
                                                                </span>
                                                            </span>
                                                            <span class="block max-h-56 divide-y divide-gray-800 overflow-y-auto px-3">
                                                                @foreach ($history['matches'] as $historyMatch)
                                                                    <span class="flex items-center justify-between gap-3 py-2 text-xs">
                                                                        <span class="min-w-0">
                                                                            <span class="block truncate text-gray-200">
                                                                                {{ $historyMatch->season?->name ?? 'Saison ' . $historyMatch->season_year }}
                                                                            </span>
                                                                            <span class="text-[10px] text-gray-500">{{ $historyMatch->date?->format('d/m/Y') }}</span>
                                                                        </span>
                                                                        <span class="flex flex-shrink-0 items-center gap-2">
                                                                            <span class="rounded px-1.5 py-0.5 font-bold {{ $historyMatch->result === 'V' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                                                                {{ $historyMatch->result === 'V' ? 'Gagné' : 'Perdu' }}
                                                                            </span>
                                                                            <span class="w-10 text-right font-semibold text-white">
                                                                                {{ $historyMatch->set_for }}–{{ $historyMatch->set_against }}
                                                                            </span>
                                                                        </span>
                                                                    </span>
                                                                @endforeach
                                                            </span>
                                                        </span>
                                                    </span>
                                                </span>
                                            @endif
                                        </div>

                                        @if ($match->pointsHistoryForPlayer($license))
                                            <div class="flex items-center gap-2 text-xs">
                                                {{-- Opponent points --}}
                                                <span class="text-gray-400">
                                                    {{ number_format($match->pointsHistoryForPlayer($license)->opponent_points, 2) }}
                                                    pts
                                                </span>

                                                {{-- Delta --}}
                                                <span
                                                    class="font-semibold
            {{ $match->pointsHistoryForPlayer($license)->delta_points >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $match->pointsHistoryForPlayer($license)->delta_points > 0 ? '+' : '' }}
                                                    {{ number_format($match->pointsHistoryForPlayer($license)->delta_points, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- RESULT + SCORE --}}
                                <div class="ctt-match-result flex items-center gap-2">
                                    <span
                                        class="w-6 text-center px-2 py-1 text-xs rounded text-white
                                        {{ $match->result == 'V' ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ $match->result }}
                                    </span>
                                    <div class="font-semibold w-10 text-right text-white">
                                        {{ $match->set_for }} - {{ $match->set_against }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let winLossChart = null

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.ctt-match-history-trigger button')
        const openTooltips = document.querySelectorAll('.ctt-match-history-tooltip.is-open')

        if (trigger) {
            const tooltip = trigger.parentElement.querySelector('.ctt-match-history-tooltip')
            const shouldOpen = !tooltip.classList.contains('is-open')

            openTooltips.forEach((openTooltip) => {
                openTooltip.classList.remove('is-open')
                openTooltip.parentElement.querySelector('button')?.setAttribute('aria-expanded', 'false')
            })

            if (shouldOpen) {
                tooltip.classList.add('is-open')
                trigger.setAttribute('aria-expanded', 'true')
            }

            return
        }

        if (event.target.closest('.ctt-match-history-tooltip')) return

        openTooltips.forEach((tooltip) => {
            tooltip.classList.remove('is-open')
            tooltip.parentElement.querySelector('button')?.setAttribute('aria-expanded', 'false')
        })
    })

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return

        document.querySelectorAll('.ctt-match-history-tooltip.is-open').forEach((tooltip) => {
            tooltip.classList.remove('is-open')
            tooltip.parentElement.querySelector('button')?.setAttribute('aria-expanded', 'false')
        })
    })

    document.addEventListener("livewire:init", () => {
        loadCharts(@json($stats['wins']), @json($stats['losses']))
        Livewire.on('refreshCharts', (event) => {
            const data = event[0]
            if (!data) return
            loadCharts(data.wins, data.losses)
        })
    })

    function loadCharts(wins = 0, losses = 0) {
        if (winLossChart) winLossChart.destroy()
        const winLoss = document.getElementById('winLossChart')
        if (winLoss) {
            winLossChart = new Chart(winLoss, {
                type: 'doughnut',
                data: {
                    labels: ['Victoires', 'Défaites'],
                    datasets: [{
                        data: [wins, losses],
                        backgroundColor: ['#22c55e', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            })
        }
    }
</script>
