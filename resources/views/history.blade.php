@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="history" class="main-body">
    @extends('layouts.navbar')
    <main id="history-section" class="main">
        <div class="section-header">
            <h2>Expenses</h2>
            <form action="{{ url('/history') }}" method="POST" id="search-form">
                @csrf
                <div class="input-field">
                    <select name="search" id="search-select">
                        <option selected disabled value="0">Search by</option>
                        @foreach ($searchBy as $key => $value)
                        @if ($search == $key)
                        <option value="{{$key}}" selected>{{$value}}</option>
                        @else
                        <option value="{{$key}}">{{$value}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="input-field" id="inp-f-s">
                    <input type="text" class="show search-input" disabled>
                    <input type="text" name="searchName" id="name-search" placeholder="Name" class="hide search-input">
                    <select name="searchCategory" id="select-search" class="hide search-input">
                        <option selected disabled>Select</option>
                        @foreach ($types as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                    <input type="date" name="searchDate" id="date-search" class="hide search-input" placeholder="Date">
                    <input type="text" name="searchPrice" id="price-search" class="hide search-input" placeholder="Price ({{$currency}})">
                    <input type="checkbox" name="chbx" id="chbx" class="hide search-chbx" value="1">
                </div>
                <input type="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
        <div class="history-expenses-table-wrap">
        <form method="post" action="{{ url('/delete-history') }}">
            @csrf
            <button type="submit" class="btn-delete" id="deleteAllBtn"><i class="fa-solid fa-trash-can"></i></button>
            <table class="expenses">
                <thead>
                    <tr class="table-head">
                        @if ($order == null || $order == 'desc')
                            <th><input type="checkbox" name="chbx" id="deleteAllChbx" onchange="toggleDeleteVisibility()"></th>
                            <th><a href="/history?sort=name&order=asc">Name</a></th>
                            <th><a href="/history?sort=category&order=asc">Category</a></th>
                            <th><a href="/history?sort=date&order=asc">Date</a></th>
                            <th><a href="/history?sort=price&order=asc">Price</a></th>
                        @else
                            <th><input type="checkbox" name="chbx"></th>
                            <th><a href="/history?sort=name&order=desc">Name</a></th>
                            <th><a href="/history?sort=category&order=desc">Category</a></th>
                            <th><a href="/history?sort=date&order=desc">Date</a></th>
                            <th><a href="/history?sort=price&order=desc">Price</a></th>
                        @endif
                        <th></th>
                        <th></th>
                    </tr>
                </thead>                                                                                                                
                <tbody>
                    @foreach ($query as $expense)
                    <tr>
                        <td class="expense-delete"><input type="checkbox" name="ids[]" class="checkbox-class" onchange="toggleDeleteVisibility()" value="{{ $expense->id }}"></td>
                        <td class="expense-name">{{$expense->name}}</td>
                        <td class="expense-cat"><i class="fa-solid {{$expense->icon_name}}" style="color:{{ $expense->color_code }}"></i> {{$expense->type_name}}</td>
                        <td class="expense-date">{{date('d/m/Y', strtotime($expense->date));}}</td>
                        <td class="expense-price">{{$expense->price}}{{$currency}}</td>
                        <td class="edit-btn"><a href="/edit-record/{{$expense->id}}"><i class="fa-solid fa-pen-to-square"></i></a></td>
                        <td class="delete-btn">
                            <form method="POST" action='/delete-record/{{$expense->id}}'>
                                @csrf
                                <button type="submit"><i class="fa-solid fa-trash-can"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        </div>
        <div id="export-expenses">
            <a href="/export">Download expenses</a>
        </div>
    </main>
    <script>
        const searchSelect = document.getElementById("search-select");
        let option2 = searchSelect.addEventListener('change', changeSearchField);
        const searchInputs = document.getElementsByClassName("search-input");
        const chbx = document.getElementById("chbx");
        
        function changeSearchField() {
            const v = searchSelect.value;
            
            searchInputs[v].classList.replace("hide" , "show");
            for ($i = 0; $i < searchInputs.length; $i++) {
                if ($i != (v)) {
                    searchInputs[$i].classList.replace("show" , "hide");
                }
            }
            
            if (v == 4) {
                chbx.classList.replace("hide" , "show");
            }
            else {
                chbx.classList.replace("show" , "hide");
            }
        }
        
        window.onload = changeSearchField;
        
        var state = false;
        var deleteChbx = document.getElementById("deleteAllChbx");
        var deleteBtn = document.getElementById("deleteAllBtn");
        var checkboxes = document.querySelectorAll('.checkbox-class');
        deleteChbx.addEventListener("click", function() {
            if (state == false) {
                state = true;
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = true;
                }
            
            }
            else {
                state = false;
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }

            }
        });

        function toggleDeleteVisibility() {
            
            var atLeastOne = Array.from(checkboxes).some(checkbox => checkbox.checked);

            if (atLeastOne || deleteChbx.checked) {
                deleteBtn.style.display = "block";
            } else {
                deleteBtn.style.display = "none";
            }
        }

    </script>
</body>
@endsection