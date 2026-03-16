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
                    {{ $s }}
                </option>
            @endforeach

        </select>

    </div>


    {{-- STATS CARDS --}}

    <div class="grid md:grid-cols-4 gap-6">

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

            <canvas id="winLossChart"></canvas>

        </div>


        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="font-semibold mb-4">
                Performance par classement
            </h2>

            <canvas id="rankingChart"></canvas>

        </div>

    </div>


    {{-- LAST MATCHES --}}

    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="font-semibold mb-4">
            Derniers matches
        </h2>

        <div class="space-y-3">

            @foreach ($lastMatches as $match)
                <div class="flex justify-between items-center border-b pb-2">

                    <div>

                        <div class="font-semibold">
                            {{ $match->opponent_firstname }} {{ $match->opponent_lastname }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $match->opponent_ranking }} • {{ $match->date }}
                        </div>

                    </div>

                    <div class="text-right">

                        <span
                            class="px-3 py-1 rounded text-white
{{ $match->result == 'V' ? 'bg-green-500' : 'bg-red-500' }}">

                            {{ $match->result == 'V' ? 'Victoire' : 'Défaite' }}

                        </span>

                        <div class="text-sm text-gray-500">

                            {{ $match->set_for }} - {{ $match->set_against }}

                        </div>

                    </div>

                </div>
            @endforeach

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("livewire:load", loadCharts)
    document.addEventListener("livewire:navigated", loadCharts)

    function loadCharts() {

        const winLoss = document.getElementById('winLossChart')

        if (winLoss) {

            new Chart(winLoss, {

                type: 'doughnut',

                data: {
                    labels: ['Victoires', 'Défaites'],

                    datasets: [{

                        data: [
                            {{ $stats['wins'] }},
                            {{ $stats['losses'] }}
                        ]

                    }]

                }

            })

        }

        const rankingChart = document.getElementById('rankingChart')

        if (rankingChart) {

            new Chart(rankingChart, {

                type: 'bar',

                data: {

                    labels: [
                        @foreach ($rankingStats as $r)
                            "{{ $r->opponent_ranking }}",
                        @endforeach
                    ],

                    datasets: [

                        {
                            label: 'Victoires',
                            data: [
                                @foreach ($rankingStats as $r)
                                    {{ $r->wins }},
                                @endforeach
                            ]
                        },

                        {
                            label: 'Défaites',
                            data: [
                                @foreach ($rankingStats as $r)
                                    {{ $r->losses }},
                                @endforeach
                            ]
                        }

                    ]

                }

            })

        }

    }
</script>
