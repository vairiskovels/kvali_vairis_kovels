@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="select-acc-section" class="main">
        <section id="select-acc-wrap">
            <div>
                <h2>Select accounts</h2>
                <form method="post" class="select-acc-form" action="{{ url('/import/transaction-selection') }}">
                    @csrf
                    @foreach ($accounts as $account)
                    <div class="input-field">
                        <div class="input-wrap">
                            <div class="iban">
                                <input type="checkbox" name="accountIds[]" value="{{ $account['id'] }}">
                                <label for="iban">{{ $account['iban'] }}</label>
                            </div>
                            <div class="account-name">
                                <p>{{ $account['name'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="input-field">
                        <input type="submit" value="Select" class="btn btn-primary">
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
<script>

        var inputs = document.getElementsByClassName("input-wrap");

        for (var i = 0; i < inputs.length; i++) {
            var state = false;
            inputs[i].addEventListener("click", function() {
                var checkbox = this.querySelector('input[type="checkbox"]');
                if (state == false) {
                    state = true;
                    checkbox.checked = true;
                
                }
                else {
                    state = false;
                    checkbox.checked = false;

                }
            });
        }

</script>
@endsection