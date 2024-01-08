@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')
<body id="dashboard" class="main-body">
    @extends('layouts.navbar')

    <main id="dashboard-page" class="main">
        <section id="dashboard-text">
            <h1 class="header">Hello, {{Auth::user()->name}}</h1>
            <p class="subheader">This is the easiest way to track your expenses</p>
            <div class="canvas pie">
                <canvas id="myChart" width="100" height="100"></canvas>
                <div class="total-chart">
                    <h2 class="total-text">Total</h2>
                    <p class="total-amout">@if ($total[0]->price == NULL)
                                --
                            @else
                                {{$total[0]->price}}{{$currency}}
                            @endif</p>
                </div>
                <p class="small-text">* In {{$monthName}}</p>
                <a href="/add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add an expense</a>
            </div>
        </section>

        <section id="dashboard-cards">
            <div class="categories-wrap">
                <p class="section-header">Categories</p>
                <div class="row">
                    @foreach ($categories as $category)
                    <div class="category-card" id="{{$category->name}}-card" style="background-color:{{$category->color_code}}" onclick="location.href='/category/{{strtolower($category->name)}}';">
                        <div class="card-header">
                            <i class="fa-solid {{$category->icon_name}}"></i>
                            <h4>{{$category->name}}</h4>
                        </div>
                        <div class="card-info"><p>
                            @if ($category->price == NULL)
                                --
                            @else
                                {{$category->price}}{{$currency}}
                            @endif
                        </p></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="expenses-table-header">
                <p class="section-header">Expenses</p>
                <a href="/history" class="expenses-full-link">Full history</a>
            </div>
            <div class="expenses-table-wrap">
                <table class="expenses">
                    <thead>
                        <tr class="table-head">
                            <th>Name</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                        <tr>
                            <td class="expense-name">{{$expense->name}}</td>
                            <td class="expense-cat"><i class="fa-solid {{$expense->icon_name}}" style="color:{{ $expense->color_code }}"></i> {{$expense->type_name}}</td>
                            <td class="expense-date">{{date('d/m/Y', strtotime($expense->date));}}</td>
                            <td class="expense-price">{{$expense->price}}{{$currency}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function getChartData() {
            var categories = {!! json_encode($categories) !!};
            var queryLabels = [];
            var queryColors = [];
            var queryData = [];
            categories.forEach(e => {
                if (e['price'] != null) {
                    queryLabels.push(e['name']);
                    queryColors.push(e['color_code']);
                    queryData.push(e['price']);
                }
            });
            if (categories[0]['price'] == null) {
                queryLabels.push('Add new expense');
                queryColors.push('#ffffff');
                queryData.push('1');
            }
            var data = [];
            data['labels'] = queryLabels;
            data['colors'] = queryColors;
            data['data'] = queryData;
            return data;
        }

        function displayChart() {
            const data = getChartData();
            const queryLabels = data['labels'];
            const queryColors = data['colors'];
            const queryData = data['data'];
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: queryLabels,
                    datasets: [{
                        label: 'Piechart',
                        data: queryData,
                        backgroundColor: queryColors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: 120,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            layout: {
                                padding: 100
                            }
                        }
                    }
                }
            });
        }

        window.onload = displayChart;
        </script>
</body>
@endsection