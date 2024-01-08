@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')
<body id="dashboard" class="main-body">
    @extends('layouts.navbar')

    <main id="delete-profile" class="main">
        <section id="delete-section" class="input-section">
            <div class="container">
                <div class="input-card">
                    <h2>Enter your password to delete account.</h2>
                    <form action="{{ url('/delete') }}" method="post" id="reset-form">
                        @csrf
                        <div class="input-field">
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" id="" placeholder="Password">
                            </div>
                        </div>
                        <div class="input-field">
                            <input type="submit" value="Delete" class="btn btn-danger">
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
@endsection