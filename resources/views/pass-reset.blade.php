@extends('layouts.main')

@section('title', 'Reset password')
@section('content')
<body>
    <section id="reset-section" class="input-section">
        <div class="container">
            <div class="input-card" id="reset-card">
                <h2>Reset password</h2>
                <form action="{{ url('/reset/password') }}" method="post" id="reset-form">
                    @csrf
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="new_password" name="new_password" placeholder="New password">
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="repeat_password" name="repeat_password" placeholder="Confirm new password">
                        </div>
                    </div>
                    <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="token" value="{{ $token }}">
                    <div class="input-field">
                        <input type="submit" value="Reset password" class="btn btn-primary">
                    </div>
                    @if (count($errors) > 0)
                        <div class="error">
                                {{ $errors->first() }}
                        </div>
                    @elseif (session()->has('error'))
                        <div class="error">
                                {{ session()->get('error') }} <a href="/">Go back</a>
                        </div>
                    @elseif(session()->has('message'))
                        <div class="success">
                            {{ session()->get('message') }} <a href="/">Go back</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
</body>
@endsection