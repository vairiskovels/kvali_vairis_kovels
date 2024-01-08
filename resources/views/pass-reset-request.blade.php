@extends('layouts.main')

@section('title', 'Reset password')
@section('content')
<body>
    <section id="reset-section" class="input-section">
        <div class="container">
            <div class="input-card" id="reset-card">
                <h2>Reset password</h2>
                <form action="{{ url('/reset') }}" method="post" id="reset-form">
                    @csrf
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-at"></i>
                            <input type="email" name="email" id="email" placeholder="Email" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-field">
                        <input type="submit" value="Send reactivation email" class="btn btn-primary">
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
                <p><a href="/login" class="text-hover" id="login">Login</a> or <a href="/register" class="text-hover" id="register">Create an account</a></p>
            </div>
        </div>
    </section>
</body>
@endsection