@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="export-section" class="main">
        <section id="export-wrap">
            <div class="input-card">
                <h2>Export expenses</h2>
                <form action="{{ url('/export') }}" method="post" id="export-form">
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-solid fa-list-ul"></i>
                            <select name="type">
                                <option value="0" {{ old('type') == '0' ? 'selected' : '' }}>All</option>
                                @foreach ($types as $type)
                                    <option value="{{$type->id}}" {{ old('type') == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-field">
                        <div class="input-wrap">
                            <i class="fa-regular fa-file"></i>
                            <select name="format">
                                <option selected disabled>Format</option>
                                <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>.csv</option>
                                <option value="tsv" {{ old('format') == 'tsv' ? 'selected' : '' }}>.tsv</option>
                                <option value="json" {{ old('format') == 'json' ? 'selected' : '' }}>.json</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-field" id="export-dates">
                        <div class="export-calendars">
                            <div class="input-wrap" id="date-from">
                                <i class="fa-solid fa-calendar-days"></i>
                                <input type="date" name="dateFrom" id="date-from-input" placeholder="Date" autocomplete="off" value="{{ old('dateFrom') }}">
                            </div>
                            <div class="input-wrap" id="date-to">
                                <input type="date" name="dateTo" id="date-to-input" placeholder="Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="export-check">
                            <input type="checkbox" name="expenseChbx" id="checkbox">
                            <label for="expenseChbx" id="chbx-label">Export all expenses</label>
                        </div>
                    </div>
                    
                    <div class="input-field">
                        <input type="submit" value="Download" class="btn btn-primary">
                    </div>
                    @if (count($errors) > 0)
                        <div class="error">
                                {{ $errors->first() }}
                        </div>
                    @else
                        <div class="success"></div>
                    @endif
                </form>
            </div>
        </section>
    </main>
    
    <script>
        const dateFrom = document.getElementById("date-from-input");
        const dateTo = document.getElementById("date-to-input");
        $('#checkbox').change(function() {
            if(this.checked) {
                dateFrom.disabled = true;
                dateTo.disabled = true;
            }
            else {
                dateFrom.disabled = false;
                dateTo.disabled = false;
            }     
        });

        var state = false;
        $('#chbx-label').on("click",function(){
            if (state == false) {
                state = true;
                $('#checkbox').prop('checked', state);
                dateFrom.disabled = true;
                dateTo.disabled = true;
                dateFrom.value = null;
                dateTo.value = null;
            }
            else {
                state = false;
                $('#checkbox').prop('checked', state);
                dateFrom.disabled = false;
                dateTo.disabled = false;

            }
        })
    </script>
</body>
@endsection