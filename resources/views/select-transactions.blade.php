@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="select-acc-section" class="main">
        <section id="select-acc-wrap">
            <div>
                <h2>Select transactions</h2>
                <form action="{{ url('/import/store-transactions') }}" method="post" class="select-acc-form">
                    @csrf
                    <div class="input-field">
                        <input type="submit" value="Select" class="btn btn-primary" id="select-trx-btn">
                    </div>
                    <div class="transaction-list">
                        @foreach ($transactions as $transaction)
                        <div class="input-field" id="select-trx-input-field">
                            <div class="input-wrap" id="select-trx-input-wrap">
                                <input type="checkbox" checked name="ids[]" value="{{ $transaction['id'] }}">
                                <div class="name select-trx-item">
                                    <label for="name">{{ $transaction['name'] }}</label>
                                    <input type="hidden" name="names[]" value="{{ $transaction['name'] }}" class="hidden-input">
                                </div>
                                <div class="amount select-trx-item">
                                    <p>{{ $transaction['amount'] }}</p>
                                    <input type="hidden" name="amounts[]" value="{{ $transaction['amount'] }}" class="hidden-input">
                                </div>
                                <div class="date select-trx-item">
                                    <p>{{ $transaction['date'] }}</p>
                                    <input type="hidden" name="dates[]" value="{{ $transaction['date'] }}" class="hidden-input">
                                </div>
                                <div class="type select-trx-item">
                                    <select name="types[]">
                                        <option value="{{$transaction['type_id']}}" selected>{{$transaction['type_name']}}</option>
                                        @foreach ($types as $type)
                                            @if ($transaction['type_id'] != $type['id'])
                                            <option value="{{$type['id']}}">{{$type['name']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
                
            </div>
        </section>
    </main>
</body>

<script>

        var inputs = document.getElementsByClassName("input-wrap");

        for (var i = 0; i < inputs.length; i++) {
            var state = false;
            var hiddenNames = ["names[]", "amounts[]", "dates[]"];
            inputs[i].addEventListener("click", function() {
                var checkbox = this.querySelector('input[type="checkbox"]');
                var hidden = this.getElementsByClassName("hidden-input");
                var select = this.querySelector('select');

                if (state == false) {
                    state = true;
                    select.setAttribute('name', 'types[]');
                    for (var j = 0; j < hidden.length; j++) {
                        var name = hiddenNames[j];
                        hidden[j].setAttribute('name', name);
                    }
                }
                else {
                    state = false;
                    select.setAttribute('name', 'x');
                    for (var j = 0; j < hidden.length; j++) {
                        hidden[j].setAttribute('name', 'x');
                    }
                }
            });
        }

        

</script>
@endsection