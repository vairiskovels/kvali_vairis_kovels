@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="add-section" class="main">
        <section id="add-wrap">
            <div class="input-card">
                <h2>Add an expense</h2>
                <form action="{{ url('/add') }}" method="post" id="register-form">
                    @csrf
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-list-ul"></i>
                            <select name="type" id="type">
                                <option selected disabled>Category</option>
                                @foreach ($types as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <input type="text" name="name" id="name" placeholder="Name">
                        </div>
                    </div>
                    
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-dollar-sign"></i>
                            <input type="text" name="price" id="price" placeholder="Price" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-calendar-days"></i>
                            <input type="date" name="date" id="date" placeholder="Date" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-field">
                        <input type="submit" value="Track" class="btn btn-primary">
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
                <div class="or-divider">
                    <div class="divider"></div>
                    <p>or</p>
                    <div class="divider"></div>
                </div>
                <div class="import">
                    <a href="/import/choose-bank">Import expenses from the bank</a>
                </div>
                
                @if(session()->has('error'))
                    <div class="error">
                        {{ session()->get('error') }}
                    </div>
                @endif
            </div>
        </section>
    </main>
</body>
@endsection