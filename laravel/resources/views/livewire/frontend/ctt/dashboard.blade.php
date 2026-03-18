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
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">

        {{-- Classement actuel --}}
        <div class="col-span-1 bg-gray-800/80 text-white rounded-xl shadow-lg p-6 flex flex-col justify-between">
            <div class="text-sm opacity-70">Classement actuel</div>
            <div class="flex items-center justify-between mt-4">
                <div class="text-4xl font-bold tracking-wide">{{ $currentRanking ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Total Matches --}}
        <div class="col-span-1 bg-gray-700/70 rounded-xl shadow-lg p-6">
            <div class="text-gray-300 text-sm">Total Matches</div>
            <div class="text-3xl font-bold text-white">{{ $stats['total'] }}</div>
        </div>

        {{-- Victoires --}}
        <div class="col-span-1 bg-green-900/70 rounded-xl shadow-lg p-6">
            <div class="text-gray-300 text-sm">Victoires</div>
            <div class="text-3xl font-bold text-green-500">{{ $stats['wins'] }}</div>
        </div>

        {{-- Défaites --}}
        <div class="col-span-1 bg-yellow-900/70 rounded-xl shadow-lg p-6">
            <div class="text-gray-300 text-sm">Défaites</div>
            <div class="text-3xl font-bold text-yellow-500">{{ $stats['losses'] }}</div>
        </div>

        {{-- Winrate --}}
        <div class="col-span-1 bg-blue-900/70 rounded-xl shadow-lg p-6">
            <div class="text-gray-300 text-sm">Winrate</div>
            <div class="text-3xl font-bold text-blue-400">{{ $stats['percentage'] }}%</div>
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
                            <div class="text-yellow-500">{{ $r->losses }}</div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="w-full h-3 bg-gray-700/40 rounded-full overflow-hidden flex">
                            <div class="h-full bg-green-500" style="width: {{ $winPct }}%"></div>
                            <div class="h-full" style="width: {{ $lossPct }}%; background-color: #fb972b;"></div>
                        </div>

                        {{-- Pourcentage --}}
                        <div class="flex justify-between text-xs font-semibold">
                            <div class="text-green-500">{{ $winPct }}%</div>
                            <div class="text-yellow-500">{{ $lossPct }}%</div>
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
                                @if ($opponent->last_ranking < $currentRanking) bg-orange-400 text-white
                                @elseif ($opponent->last_ranking > $currentRanking) bg-teal-400 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ $opponent->last_ranking }}
                            </div>
                        </div>
                    </div>

                    {{-- BOTTOM ROW: WINS/LOSSES + PROGRESS BAR --}}
                    <div class="flex flex-col gap-1 mt-2">
                        <div class="flex justify-between text-xs font-medium">
                            <span class="text-green-500">{{ $opponent->wins }} Victoires ({{ $winsPercent }}%)</span>
                            <span class="text-yellow-500">{{ $opponent->losses }} Défaites
                                ({{ $lossesPercent }}%)
                            </span>
                        </div>

                        <!-- Progress bar juste en dessous -->
                        <div class="relative w-full h-2 bg-gray-700/40 rounded-full overflow-hidden">
                            <div class="absolute h-full rounded-full"
                                style="width: {{ $winsPercent }}%; background-color: #22c55e;"></div>
                            <div class="absolute h-full rounded-full"
                                style="width: {{ $lossesPercent }}%; background-color: #fb972b; left: {{ $winsPercent }}%;">
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

                    {{-- CLUB + DATE --}}
                    <div class="mb-2">
                        <h2 class="font-semibold text-lg text-white">{{ $matches->first()->opponent_club }}</h2>
                        <div class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($matches->first()->date)->format('d M Y') }}
                        </div>
                    </div>

                    {{-- MATCHES LIST --}}
                    <div class="space-y-2">
                        @foreach ($matches as $match)
                            <div class="flex justify-between items-center">
                                {{-- OPPONENT + RANKING --}}
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                        @if ($match->opponent_ranking < $currentRanking) bg-orange-400 text-white
                                        @elseif ($match->opponent_ranking > $currentRanking) bg-teal-400 text-white
                                        @else bg-gray-500 text-white @endif">
                                        {{ $match->opponent_ranking }}
                                    </div>
                                    <div class="font-medium text-white">
                                        {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}
                                    </div>
                                </div>

                                {{-- RESULT + SCORE --}}
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-6 text-center px-2 py-1 text-xs rounded text-white
                                        {{ $match->result == 'V' ? 'bg-green-500' : 'bg-yellow-500' }}">
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
                        backgroundColor: ['#22c55e', '#fb972b'],
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
