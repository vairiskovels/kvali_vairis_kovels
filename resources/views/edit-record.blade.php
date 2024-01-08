@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="edit-record" class="main-body">
    @extends('layouts.navbar')

    <main id="add-section" class="main">
        <section id="add-wrap">
            <div class="input-card">
                <h2>Edit record</h2>
                <form action="{{ url('/edit-record') }}" method="post" id="register-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $record[0]->id}}">
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-list-ul"></i>
                            <select name="type" id="type">
                                <option value="{{$record[0]->type_id}}" selected>{{$record[0]->type_name}}</option>
                                @foreach ($types as $type)
                                    @if ($type->name != $record[0]->type_name)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <input type="text" name="name" id="name" placeholder="Name" value="{{$record[0]->name}}">
                        </div>
                    </div>
                    
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-dollar-sign"></i>
                            <input type="text" name="price" id="price" placeholder="Price" autocomplete="off" value="{{$record[0]->price}}">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-calendar-days"></i>
                            <input type="date" name="date" id="date" placeholder="Date" autocomplete="off" value="{{$record[0]->date}}">
                        </div>
                    </div>
                    <div class="input-field">
                        <input type="submit" value="Edit" class="btn btn-primary">
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