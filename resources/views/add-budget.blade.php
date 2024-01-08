@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="add-section" class="main">
        <section id="add-wrap">
            <div class="input-card">
                <h2>{{ $title }}</h2>
                <form action="{{ url('/add-budget') }}" method="post" id="budget-form">
                    @csrf
                    
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-dollar-sign"></i>
                            @if ($budgetThisMonth > 0)
                            <input type="text" name="amount" id="amount" placeholder="Amount" autocomplete="off" value="{{ $budgetThisMonth }}">
                            @else
                            <input type="text" name="amount" id="amount" placeholder="Amount" autocomplete="off">
                            @endif
                            
                        </div>
                    </div>
                    <input type="hidden" name="type_id" value="{{ $id }}">
                    <div class="input-field">
                        @if ($budgetThisMonth > 0)
                        <input type="submit" value="Edit" class="btn btn-primary">
                        @else
                        <input type="submit" value="Add" class="btn btn-primary">
                        @endif
                    </div>
                    @if (count($errors) > 0)
                        <div class="error">
                                {{ $errors->first() }}
                        </div>
                    @elseif(session()->has('message'))
                        <div class="success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                </form>
            </div>
        </section>
    </main>
</body>
@endsection