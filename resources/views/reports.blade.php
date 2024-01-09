@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')
<body id="reports" class="main-body">
    @extends('layouts.navbar')

    <main id="reports-section" class="main">
        <div class="reports-wrap">
            <div class="section-header-wrap">
                <h2 class="section-header" id="section-header">Total expenses in {{ $selectedYear }}</h2>
                <div class="selects">
                    <select name="reports-select" id="reports-select">
                        <option value="1" selected>Expenses in a year</option>
                        <option value="2">Expenses by category</option>
                        <option value="3">Top 10 expenses this month</option>
                    </select>
                    <select name="years" id="categories-years">
                        @foreach ($years as $data)
                            @if ($data->year == $selectedYear)
                                <option value="{{ $data->year }}" selected>{{ $data->year }}</option>
                            @else
                                <option value="{{ $data->year }}">{{ $data->year }}</option>
                            @endif
                            
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="report-wrap show" id="expenses-by-month">
                <div class="report-card">
                    <div class="canvas">
                        <canvas id="byMonthTotal" height="420px" width="850px"></canvas>
                    </div>
                </div>
            </div>
            <div class="report-wrap hide" id="expenses-by-category-bar">
                <div class="report-card" id="expenses-by-category-card">
                    <div class="canvas" id="by-category-bar">
                        <canvas id="byMonthCategory" height="420px" width="850px"></canvas>
                    </div>
                </div>
            </div>
            <div class="report-wrap hide" id="top-10-expenses">
                <div class="report-card">
                    <div class="canvas" id="top10-bar">
                        <canvas id="top10" height="420px" width="850px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ URL::asset('js/app.js') }}"></script>
    <script>

        const report1 = document.getElementById('byMonthTotal').getContext('2d');
        const report2 = document.getElementById('byMonthCategory').getContext('2d');
        const report3 = document.getElementById('top10').getContext('2d');

        var byMonthQuery = {!! json_encode($byMonth) !!};
        var byCategoryQuery = {!! json_encode($byCategory) !!};
        var top10 = {!! json_encode($top10) !!};

        console.log(byCategoryQuery);

        const months = ['January', 'February', 'March', 'April','May','June','July','August','September','October','November','December'];

        // Data for byMonth chart
        var byMonthData = [0,0,0,0,0,0,0,0,0,0,0,0];
        byMonthQuery.forEach(e => {
            byMonthData[e['month']-1] = parseFloat(e['price']);
        });
        
        // Data for top10 chart
        var top10Prices = [];
        var top10Names = [];
        var top10Colors = [];
        top10.forEach(e => {
            top10Names.push(e['name']);
            top10Prices.push(e['price']);
            top10Colors.push(e['color']);
        });

        // Data for byCategory chart
        let categoryNames = [];
        let categoryColors = [];
        let categoryData = [];
        let fullData = [];
        let byCategoryDict = {};
        let maxValue = 0;
        byCategoryQuery.forEach(e => {
            if (!categoryNames.includes(e['name'])) {
                categoryNames.push(e['name']);
                categoryColors.push(e['color']);
                byCategoryDict[e['name']] = {};
            }
            let name = e['name']
            byCategoryDict[name][e['month']] = e['price'];
        });

        for (const [key, value] of Object.entries(byCategoryDict)) {
            let new_arr = [];
            for (const [nkey, nvalue] of Object.entries(value)) {
                new_arr.push(value[nkey]);
            }
            let m = Math.max.apply(Math, new_arr);
            if (m > maxValue) {
                maxValue = m;
            }
            categoryData.push(new_arr);
        }

        for ($i = 0; $i < categoryNames.length; $i++) {
            let new_dict = {};
            new_dict['label'] = categoryNames[$i];
            new_dict['data'] = categoryData[$i];
            new_dict['borderColor'] = categoryColors[$i];
            new_dict['backgroundColor'] = categoryColors[$i];
            new_dict['tension'] = 0.2;
            fullData.push(new_dict);
        }

        // By month chart
        const chart1 = new Chart(report1, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Expenses',
                    data: byMonthData,
                    fill: true,
                    borderColor: '#ff6384',
                    backgroundColor: '#ff638480',
                    tension: 0.2
                }]
            },
            options: {
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: Math.max.apply(Math, categoryData) * 1.15
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // By category chart
        const chart2 = new Chart(report2, {
            type: 'line',
            data: {
                labels: months,
                datasets: fullData,
                fill: true,
                borderWidth: 1
            },
            options: {
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: maxValue * 1.1
                    }
                }
            }
        });

        // Top10 chart
        const chart3 = new Chart(report3, {
            type: 'bar',
            data: {
                labels: top10Names,
                datasets: [{
                    label: 'Top 10',
                    data: top10Prices,
                    fill: true,
                    backgroundColor: top10Colors,
                }]
            },
            options: {
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: Math.max.apply(Math, top10Prices) * 1.15,
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        $('#categories-years').on('change', function() {
            var y = this.value;
            location.href = '/reports' + '?year='+y;
        });
        </script>
</body>
@endsection