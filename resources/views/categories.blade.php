@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')
<body id="category" class="main-body">
    @extends('layouts.navbar')
    <main id="category-page" class="main">
        <section id="chart-section">
            <div class="section-header">
                <h2 class="header-title">How much did you spend on <span id="month"></span> by months</h2>
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
            <div class="canvas category-bar">
                <canvas id="myChart" height="400px"></canvas>
            </div>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function sortCategotyData(categoryData) {
            data = [0,0,0,0,0,0,0,0,0,0,0,0];
            categoryData.forEach(e => {
                data[e['month']-1] = parseFloat(e['price']);
            });
            return data;
        }

        function displayChart() {
            var categoryData = {!! json_encode($category) !!};
            const months = ['January', 'February', 'March', 'April','May','June','July','August','September','October','November','December'];
            var color = categoryData[0]['color'];
            var categoryName = categoryData[0]['name'];;
            var data = sortCategotyData(categoryData);
            
            document.getElementById("month").innerHTML = categoryName.toLowerCase();
            document.getElementById("month").style.color = color;
    
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: categoryName,
                        data: data,
                        fill: true,
                        backgroundColor: color.concat('80'),
                        borderColor: color,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            suggestedMin: 0,
                            suggestedMax: Math.max.apply(Math, data) * 1.1
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
    
            $('select').on('change', function() {
                var y = this.value;
                location.href = '/category/' + categoryName.toLowerCase() + '?year=' + y;
            });
        }

        window.onload = displayChart;

        </script>

</body>
@endsection