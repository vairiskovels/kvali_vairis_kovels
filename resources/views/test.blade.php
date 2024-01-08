
<table class="expenses">
    <thead>
        <tr class="table-head">
            <th>Name</th>
            <th>Category</th>
            <th>Date</th>
            <th>Amount</th>
            <th></th>
            <th></th>
        </tr>
    </thead>                                                                                                                
    <tbody>
        @foreach ($expenses as $expense)
        <tr>
            <td class="expense-name">{{$expense->name}}</td>
            <td class="expense-cat"><i class="fa-solid {{$expense->icon_name}}"></i> {{$expense->type_name}}</td>
            <td class="expense-date">{{date('d/m/Y', strtotime($expense->date));}}</td>
            <td class="expense-price">{{$expense->price}}€</td>
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