@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="dashboard" class="main-body">
    @extends('layouts.navbar')

    <main id="budget-page" class="main">
        <div class="monthly-budget">
            <div class="budget-amounts">
                <div id="month-budget" class="budget-amount-body">
                    <h1>Budget in {{ $monthName }}</h1>
                    @if ($budgetThisMonth == 0)
                    <h2 class="budget-amount"><a href="/add-budget?id=99">--</a></h2>
                    @else
                    <h2 class="budget-amount"><a href="/add-budget?id=99">{{ $budgetThisMonth }}{{$currency}}</a></h2>
                    @endif
                </div>
                <div class="budget-devider"></div>
                @if ($budgetThisMonth == 0)
                <div id="saved-amount" class="budget-amount-body">
                    <h2>Saved</h2>
                    <h3>--</h3>
                </div>
                @elseif ($saved >= 0)
                <div id="spent-amount" class="budget-amount-body">
                    <h2>Spent</h2>
                    <h3>{{ $budgetThisMonth - $saved }}{{$currency}}</h3>
                </div>
                <div id="saved-amount" class="budget-amount-body">
                    <h2>Saved</h2>
                    <h3>{{ $saved }}{{$currency}}</h3>
                </div>
                @else
                <div id="overspent-amount" class="budget-amount-body">
                    <h2>Overspent</h2>
                    <h3>{{ $saved }}{{$currency}}</h3>
                </div>
                @endif
            </div>
            <div class="budget-graph">
                <div class="graph-bar" id="spent-bar"></div>
            </div>
        </div>


        <div class="detailed-budget">
            <div class="detailed-budget-col">
                @for ($i = 0; $i < 4; $i++)
                    <div class="detailed-budget-row" id="budget-row-{{ $categories[$i]->id }}"><a href="/add-budget?id={{ $categories[$i]->id }}&name={{ $categories[$i]->name }}">Budget for {{ $categories[$i]->name }}</a><div class="budget-bar" id="budget-bar-{{ $categories[$i]->id }}"></div></div>
                @endfor
            </div>
            <div class="detailed-budget-col">
                @for ($i = 4; $i < 8; $i++)
                    <div class="detailed-budget-row" id="budget-row-{{ $categories[$i]->id }}"><a href="/add-budget?id={{ $categories[$i]->id }}&name={{ $categories[$i]->name }}">Budget for {{ $categories[$i]->name }}</a><div class="budget-bar" id="budget-bar-{{ $categories[$i]->id }}"></div></div>
                @endfor
            </div>
        </div>

    </main>

    <script>
        const budgetThisMonth = {!! json_encode($budgetThisMonth) !!};
        const budgetByCategory = {!! json_encode($categoryBudgetQuery) !!};
        const spentThisMonth = budgetThisMonth - {!! json_encode($saved) !!};
        const spentPercantage = (spentThisMonth * 100) / budgetThisMonth;
        const categories = {!! json_encode($categories) !!};
        const spentByCategory = [];

        for (i in categories) {
            spentByCategory.push(categories[i].price);
        }

        console.log(budgetByCategory)
        for (i in budgetByCategory) {
            const id = budgetByCategory[i].type_id;
            const budgetBarName = 'budget-bar-' + id;
            let categoryBudgetPercantage = (spentByCategory[id-1] * 100) / budgetByCategory[i].amount;
            if (categoryBudgetPercantage > 100) {
                categoryBudgetPercantage = 100;
            }

            document.getElementById(budgetBarName).style.width = categoryBudgetPercantage+"%";
        }

        window.onload = (event) => {
            document.getElementById("spent-bar").animate(
            [
                { width: "0%"},
                { width: spentPercantage+"%"}
            ],
            {
                duration: 825,
                iterations: 1,
                easing: "ease-in-out",
                fill: "forwards"
            },
            );
        };

        const budgetBars = document.getElementsByClassName("budget-bar");  

        
        // for (i in x) {
        //     console.log(x[i].amount, x[i].type_id);
        // }

        // const budgetRow = document.getElementsByClassName("detailed-budget-row");    
        // for (var i = 0; i < budgetRow.length; i++) {
        //     budgetRow[i].addEventListener("mouseover", function(){
        //         console.log(1);
        //     });
        // }

    </script>
</body>
@endsection