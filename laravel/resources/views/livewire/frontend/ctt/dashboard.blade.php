<div class="p-6 max-w-7xl mx-auto space-y-8">

    {{-- HEADER --}}

    <div class="flex justify-between items-center">

        <h1 class="text-3xl font-bold">
            🏓 CTT Performance Dashboard
        </h1>

        <select wire:model.live="season" class="border rounded-lg px-4 py-2">

            <option value="all">Toutes saisons</option>

            @foreach ($seasons as $s)
                <option value="{{ $s }}">
                    {{ $s - 1 }} - {{ $s }}
                </option>
            @endforeach

        </select>

    </div>


    {{-- STATS CARDS --}}

    <div class="grid md:grid-cols-5 gap-6">
        <div
            class="bg-gradient-to-br from-gray-900 to-gray-800 text-white rounded-xl shadow p-6 flex flex-col justify-between">

            <div class="text-sm opacity-70">
                Classement actuel
            </div>

            <div class="flex items-center justify-between mt-4">

                <div class="text-4xl font-bold tracking-wide">
                    {{ $currentRanking ?? 'N/A' }}
                </div>

            </div>

        </div>

        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Total Matches
            </div>

            <div class="text-3xl font-bold">
                {{ $stats['total'] }}
            </div>

        </div>


        <div class="bg-green-50 rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Victoires
            </div>

            <div class="text-3xl font-bold text-green-600">
                {{ $stats['wins'] }}
            </div>

        </div>


        <div class="bg-red-50 rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Défaites
            </div>

            <div class="text-3xl font-bold text-red-600">
                {{ $stats['losses'] }}
            </div>

        </div>


        <div class="bg-blue-50 rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Winrate
            </div>

            <div class="text-3xl font-bold text-blue-600">
                {{ $stats['percentage'] }}%
            </div>

        </div>

    </div>


    {{-- CHARTS --}}

    <div class="grid md:grid-cols-2 gap-6">

        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="font-semibold mb-4">
                Win / Loss
            </h2>

            <div class="relative h-72">
                <canvas id="winLossChart"></canvas>
            </div>

        </div>

        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="font-semibold mb-4">
                Performance par classement
            </h2>

            <div class="relative h-72">
                <canvas id="rankingChart" wire:ignore></canvas>
            </div>

        </div>
    </div>
    {{-- LAST MATCHES --}}

    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="font-semibold mb-4">
            Derniers matches
        </h2>

        <div class="space-y-3">

            <div class="grid md:grid-cols-3 gap-6 text-sm">

                @foreach ($matchesGrouped as $matches)
                    <div class="bg-white rounded-xl shadow p-6">

                        <div class="mb-4">

                            <h2 class="font-semibold text-lg">
                                {{ $matches->first()->opponent_club }}
                            </h2>

                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($matches->first()->date)->format('d M Y') }}
                            </div>

                        </div>

                        <div class="space-y-2">

                            @foreach ($matches as $match)
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-sm">
                                            {{ $match->opponent_ranking }}
                                        </div>
                                        <div class="font-medium">
                                            {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <!-- Badge V/D avec largeur fixe -->
                                        <span
                                            class="w-6 text-center px-2 py-1 text-xs rounded text-white
        {{ $match->result == 'V' ? 'bg-green-500' : 'bg-red-500' }}">
                                            {{ $match->result }}
                                        </span>

                                        <!-- Sets avec largeur fixe -->
                                        <div class="font-semibold w-10 text-right">
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

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let winLossChart = null
    let rankingChartInstance = null

    document.addEventListener("livewire:init", () => {

        loadCharts(
            @json($stats['wins']),
            @json($stats['losses']),
            @json($rankingStats->toArray())
        )

        Livewire.on('refreshCharts', (event) => {

            const data = event[0] // ✅ IMPORTANT

            if (!data || !data.ranking) return

            loadCharts(
                data.wins,
                data.losses,
                data.ranking
            )
        })
    })

    function loadCharts(wins = 0, losses = 0, ranking = []) {

        if (winLossChart) {
            winLossChart.destroy()
        }

        if (rankingChartInstance) {
            rankingChartInstance.destroy()
        }

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

        const rankingChart = document.getElementById('rankingChart')

        if (rankingChart) {

            const labels = ranking.map(r => r.opponent_ranking ?? '')
            const winsData = ranking.map(r => r.wins ?? 0)
            const lossesData = ranking.map(r => r.losses ?? 0)

            rankingChartInstance = new Chart(rankingChart, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Victoires',
                            backgroundColor: '#22c55e',
                            data: winsData
                        },
                        {
                            label: 'Défaites',
                            backgroundColor: '#ef4444',
                            data: lossesData
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            })
        }
    }
</script>
