<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\User;
use App\Models\Expense;
use Carbon\Carbon;
use Validator;
use Auth;

class MainController extends Controller
{

    public function index()
    {
        if (Auth::user()) { 
            $expenses = Expense::select([
                'expenses.name',
                'expenses.price',
                'expenses.date',
                'types.name as type_name',
                'types.icon_name as icon_name',
                'types.color_code as color_code'
            ])
            ->join('types', 'types.id', '=', 'expenses.type_id')
            ->where('user_id', auth()->user()->id)
            ->orderByRaw('expenses.date DESC, expenses.name ASC')
            ->limit(15)
            ->get();
            
            $thisMonth = $this->getThisMonth();
            $thisYear = $this->getThisYear();
            $monthName = Carbon::now()->format('F');
            $categories = DB::select("SELECT types.id as id, types.name as name, types.icon_name as icon_name, SUM(expenses.price) as price, color_code
                                    FROM types
                                    LEFT JOIN expenses on types.id = expenses.type_id AND expenses.user_id = ? AND MONTH(expenses.date) = ? AND YEAR(expenses.date) = ?
                                    WHERE types.id != 99
                                    GROUP BY types.id
                                    ORDER BY SUM(expenses.price) desc
                                    LIMIT 8", [auth()->user()->id, $thisMonth, $thisYear]);

            $total = DB::select("SELECT SUM(price) as price
                                FROM expenses
                                WHERE user_id = ? AND MONTH(expenses.date) = ? AND YEAR(expenses.date) = ?", [auth()->user()->id, $thisMonth, $thisYear]);
            $currency =  User::where('id', auth()->user()->id)->value('currency');

            return view('index', compact('expenses', 'categories', 'monthName', 'currency'))->with(['total' => $total]);
        } else {
            return view('login');
        }
    }

    function addExpense()
    {
        $types = Type::select('id', 'name', 'icon_name', 'color_code')->whereNotIn('id', ['99'])->get();
        return view('add', compact('types'));
    }

    public function storeExpense(Request $request)
    {   
        $this->validate($request, [
            'type'      => 'required|numeric|not_in:99  ',
            'name'      => 'required|max:255',
            'price'      => 'required|numeric|min:0.01',
            'date'      => 'required|date|max:'.(date('Y'))
        ]);

        $expense = new Expense();
        $expense->type_id = $request->get('type');
        $expense->user_id = auth()->user()->id;
        $expense->name = ucfirst($request->name);
        $expense->price = $request->price;
        $expense->date = Carbon::parse($request->date);
        $expense->save();
        return redirect('/add')->with('message', "Expense has succesfully been added to the list.");
    }

    public function category(Request $request, $id) {
        $years = DB::select("SELECT DISTINCT YEAR(date) as year
                             FROM expenses
                             JOIN types on expenses.type_id = types.id
                             WHERE types.name = ?", [$id]);

        $selectedYear = $request->year;
        if ($selectedYear == NULL) {
            $selectedYear = $this->getThisYear();
        }

        $category = DB::select("SELECT types.name as name, SUM(price) as price, MONTH(date) as month, color_code as color
                                FROM expenses
                                JOIN types on expenses.type_id = types.id
                                WHERE types.name = ? AND YEAR(expenses.date) = ? AND expenses.user_id = ?
                                GROUP BY MONTH(expenses.date), name, color
                                ORDER BY MONTH(expenses.date) ASC", [$id, $selectedYear, auth()->user()->id]);

        return view('categories', compact('category', 'years', 'selectedYear'));
    }

    public function reports(Request $request) {
        $years = DB::select("SELECT DISTINCT YEAR(date) as year
                             FROM expenses
                             WHERE expenses.user_id = ?", [auth()->user()->id]);

        $selectedYear = $request->year;
        if ($selectedYear == NULL) {
            $selectedYear = $this->getThisYear();
        }
        
        $byMonth = $this->getReportByMonth($selectedYear);
        $byCategory = $this->getReportByCategory($selectedYear);
        $top10 = $this->getReportTop10();
        
        return view('reports', compact('byMonth', 'byCategory', 'top10', 'years', 'selectedYear'));
    }

    public function getReportByMonth($year) {

        $byMonthReport = DB::select("SELECT MONTH(date) as month, SUM(price) as price 
                                FROM expenses
                                WHERE user_id = ? AND YEAR(date) = ?
                                GROUP BY MONTH(date)", [auth()->user()->id, $year]);
        return $byMonthReport;
    }

    public function getReportByCategory($year) {
        $byCategoryReport = DB::select("SELECT types.name as name, SUM(price) as price, MONTH(date) as month, color_code as color
                                FROM expenses
                                JOIN types on expenses.type_id = types.id
                                WHERE YEAR(expenses.date) = ? AND expenses.user_id = ?
                                GROUP BY MONTH(expenses.date), name, color
                                ORDER BY MONTH(expenses.date) ASC", [$year, auth()->user()->id]);
        return $byCategoryReport;
    }

    public function getReportTop10() {
        $top10Report = DB::select("SELECT expenses.name as name, expenses.price as price, color_code as color
                            FROM expenses
                            JOIN types on expenses.type_id = types.id
                            WHERE MONTH(expenses.date) = ? AND expenses.user_id = ?
                            ORDER BY expenses.price DESC
                            LIMIT 10", [Carbon::now()->month, auth()->user()->id]);
        return $top10Report;
    }

    public function history(Request $request) {
        $search = $request->search;
        $sort = $request->sort;
        if ($sort == 'category') {
            $sort = 'type_id';
        }
        $order = $request->order;
        $types = Type::select('id', 'name', 'icon_name', 'color_code')->whereNotIn('id', ['99'])->get();
        $expenses = Expense::select([
            'expenses.id',
            'expenses.name',
            'expenses.price',
            'expenses.date',
            'types.name as type_name',
            'types.icon_name as icon_name',
            'types.color_code as color_code'
        ])
        ->join('types', 'types.id', '=', 'expenses.type_id')
        ->where('user_id', auth()->user()->id);
        
        $currency =  User::where('id', auth()->user()->id)->value('currency');

        if ($search == 1){
            if ($request->searchName != null) {
                $expenses->where('expenses.name', 'like', '%'.$request->searchName.'%')->get();
            }
        }
        else if ($search == 2){
            if ($request->searchCategory != null) {
                $expenses->where('expenses.type_id', $request->searchCategory);
            }
        }
        else if ($search == 3){
            if ($request->searchDate != null) {
                $expenses->where('expenses.date', Carbon::parse($request->searchDate));
            }
        }
        else if ($search == 4){
            if ($request->searchPrice != null) {
                if ($request->chbx == '1') {
                    $expenses->where('expenses.price', '<=',$request->searchPrice)->orderByRaw('expenses.date DESC, expenses.price DESC');
                }
                else {
                    $expenses->where('expenses.price', '>=',$request->searchPrice)->orderByRaw('expenses.date DESC, expenses.price DESC');
                }
            }
        }

        if ($sort != null) {
            $query = $expenses->orderByRaw('expenses.'.$sort.' '.$order)->get();
        }
        else {
            $query = $expenses->orderByRaw('expenses.date DESC, expenses.name ASC')->get();
        }
        $searchBy = ['1'=>'Name', '2'=>'Category', '3'=>'Date', '4'=>'Price'];

        return view('history', compact('query', 'types', 'order', 'search', 'searchBy', 'currency'));
    }

    public function deleteHistory(Request $request) {
        $ids = $request->input('ids', []);
        foreach ($ids as $id) {
            Expense::where('id', $id)->where('user_id', auth()->user()->id)->delete();
        }
        return redirect('/history');
    }

    public function editRecord($id)
    { 

        $checkId = Expense::select(['*'])
                    ->where('expenses.id', $id)
                    ->get();

        $types = Type::all();
        $record = Expense::select([
            'expenses.id',
            'expenses.name',
            'expenses.price',
            'expenses.date',
            'types.name as type_name',
            'types.id as type_id'
        ])
        ->join('types', 'types.id', '=', 'expenses.type_id')
        ->where('expenses.id', $id)
        ->where('user_id', auth()->user()->id)
        ->get();

        return view('edit-record', compact('types', 'record'));
    }

    public function updateRecord(Request $request)
    {
        $this->validate($request, [
            'type'      => 'required|numeric',
            'name'      => 'required|max:255',
            'price'     => 'required|numeric|min:0.01',
            'date'      => 'required|date|max:'.(date('Y'))
        ]);

        $expense = Expense::find($request->get('id'));
        $expense->type_id = $request->get('type');
        $expense->user_id = auth()->user()->id;
        $expense->name = ucfirst($request->name);
        $expense->price = $request->price;
        $expense->date = Carbon::parse($request->date);
        $expense->save();
        return redirect('/history');
    }

    public function getThisMonth() {
        return Carbon::now()->month;
    }

    public function getThisYear() {
        return Carbon::now()->year;
    }

    public function destroyRecord($id)
    {
        Expense::where('id', $id)->where('user_id', auth()->user()->id)->delete();
        return redirect('/history');
    }
}
