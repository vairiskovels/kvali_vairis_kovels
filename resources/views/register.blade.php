@extends('layouts.main')

@section('title', 'Register')
@section('content')
<body>
    <section id="register-section" class="input-section">
        <div class="container">
            <div class="input-card">
                <h2>Create an account</h2>
                <form action="{{ url('/register') }}" method="post" id="register-form">
                    @csrf
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-id-card-clip"></i>
                            <input type="text" name="name" id="name" placeholder="Full name">
                        </div>
                    </div>
                    
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" id="username" placeholder="Username">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-at"></i>
                            <input type="email" name="email" id="email" placeholder="Email" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Password" autocomplete="off">
                        </div>
                    </div>
                    <div class="input-field">
                        <input type="submit" value="Register" class="btn btn-primary">
                    </div>
                    @if (count($errors) > 0)
                        <div class="error">
                                {{ $errors->first() }}
                        </div>
                    @endif 
                </form>
                <p>Already have an account? <a href="/login" id="register" class="text-hover">Login here</a></p>
            </div>
        </div>
    </section>
</body>
@endsection
