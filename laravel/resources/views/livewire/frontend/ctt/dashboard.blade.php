<div class="p-6 max-w-7xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-3xl font-bold text-white">🏓 CTT Dashboard</h1>

        <select wire:model.live="season"
            class="border rounded-lg px-4 py-2 bg-gray-800 text-white border-gray-700 self-end sm:self-auto">
            <option value="all">Toutes saisons</option>
            @foreach ($seasons as $s)
                <option value="{{ $s }}">
                    {{ $s - 1 }} - {{ $s }}
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
                <div class="text-5xl font-bold tracking-wide leading-none">
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

    {{-- TOP 10 OPPONENTS --}}
    <h2 class="text-lg font-semibold mb-6 text-white">Top 10 adversaires</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($this->getTopOpponents() as $index => $opponent)
            @php
                $total = $opponent->total_matches;
                $winsPercent = $total ? round(($opponent->wins / $total) * 100) : 0;
                $lossesPercent = $total ? round(($opponent->losses / $total) * 100) : 0;
            @endphp
            <div class="bg-gray-700/40 rounded-xl shadow-lg p-4 flex gap-4 items-stretch">

                {{-- POSITION LEFT --}}
                <div class="w-12 flex flex-col justify-center items-center font-bold text-gray-300 text-lg">
                    #{{ $index + 1 }}
                </div>

                {{-- RIGHT CONTENT --}}
                <div class="flex-1 flex flex-col justify-between">
                    {{-- TOP ROW: NAME + CLUB + RANKING --}}
                    <div class="flex justify-between items-center gap-4">
                        <div>
                            <div class="text-sm font-bold text-white">{{ $opponent->opponent_firstname }}
                                {{ $opponent->opponent_lastname }}</div>
                            <div class="text-xs text-gray-400">{{ $opponent->opponent_club }}</div>
                            <div class="text-xs text-gray-500">{{ $total }} matchs</div>
                        </div>

                        {{-- RANKING --}}
                        <div>
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                @if ($opponent->last_ranking < $season_detail->ranking) bg-orange-400 text-white
                                @elseif ($opponent->last_ranking > $season_detail->ranking) bg-teal-400 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ $opponent->last_ranking }}
                            </div>
                        </div>
                    </div>

                    {{-- BOTTOM ROW: WINS/LOSSES + PROGRESS BAR --}}
                    <div class="flex flex-col gap-1 mt-2">
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-green-500">{{ $opponent->wins }} Victoires ({{ $winsPercent }}%)</span>
                            <span class="text-red-400">{{ $opponent->losses }} Défaites
                                ({{ $lossesPercent }}%)
                            </span>
                        </div>

                        <!-- Progress bar juste en dessous -->
                        <div class="relative w-full h-2 bg-gray-700/40 rounded-full overflow-hidden">
                            <div class="absolute h-full rounded-full"
                                style="width: {{ $winsPercent }}%; background-color: #22c55e;"></div>
                            <div class="absolute h-full rounded-full"
                                style="width: {{ $lossesPercent }}%; background-color: #ef4444; left: {{ $winsPercent }}%;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

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
                            $totalDelta = $matches->sum(fn($m) => $m->pointsHistory?->delta_points ?? 0);
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
                            <div class="flex justify-between items-center">
                                {{-- OPPONENT + RANKING --}}
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                        @if ($match->opponent_ranking < $season_detail->ranking) bg-orange-400 text-white
                                        @elseif ($match->opponent_ranking > $season_detail->ranking) bg-teal-400 text-white
                                        @else bg-gray-500 text-white @endif">
                                        {{ $match->opponent_ranking }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-white">
                                            {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}
                                        </div>

                                        @if ($match->pointsHistory)
                                            <div class="flex items-center gap-2 text-xs">
                                                {{-- Opponent points --}}
                                                <span class="text-gray-400">
                                                    {{ number_format($match->pointsHistory->opponent_points, 2) }} pts
                                                </span>

                                                {{-- Delta --}}
                                                <span
                                                    class="font-semibold
            {{ $match->pointsHistory->delta_points >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $match->pointsHistory->delta_points > 0 ? '+' : '' }}
                                                    {{ number_format($match->pointsHistory->delta_points, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- RESULT + SCORE --}}
                                <div class="flex items-center gap-2">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let winLossChart = null

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
