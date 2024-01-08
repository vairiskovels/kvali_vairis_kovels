@extends('layouts.main')

@section('title', 'Login')
@section('content')
<body>
    <section id="login-section" class="input-section">
        <div class="container">
            <div class="input-card">
                @if(isset(Auth::user()->username))
                    <script>window.location = '/'</script>
                @endif
                <h2>Login</h2>
                <form action="{{ url('/login/auth') }}" method="post" id="login-form">
                    @csrf
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" id="username" placeholder="Username">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Password">
                        </div>
                    </div>
                    <div class="input-field">
                        <input type="submit" value="Login" name="login" class="btn btn-primary">
                    </div>
                    @if (count($errors) > 0)
                        <div class="error">
                                {{ $errors->first() }}
                        </div>
                    @elseif(session()->has('message'))
                        <div class="error">
                            {{ session()->get('message') }}
                        </div>
                    @endif         
                </form>
                <div id="forgot-password"><a class="text-hover" href="/reset">Forgot password?</a></div>
                <p>Don't have an account? <a href="/register" class="text-hover" id="register">Register here</a></p>
            </div>
        </div>
    </section>
</body>
@endsection