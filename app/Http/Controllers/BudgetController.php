<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\User;
use App\Models\Budget;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function budget(Request $request)
    {   
        $date = Carbon::now();
        $monthName = $date->format('F');
        $thisMonth = $this->getThisMonth();
        $thisYear = $this->getThisYear();

        $budgetQuery = DB::select("SELECT amount 
                                    FROM budgets
                                    WHERE MONTH(budgets.date) = ? AND YEAR(budgets.date) = ? AND budgets.user_id = ? AND budgets.type_id = ?", [$thisMonth, $thisYear, auth()->user()->id, 99]);

        $categoryBudgetQuery = DB::select("SELECT amount, type_id 
                                    FROM budgets
                                    WHERE MONTH(budgets.date) = ? AND YEAR(budgets.date) = ? AND budgets.user_id = ? AND budgets.type_id != ?", [$thisMonth, $thisYear, auth()->user()->id, 99]);
        $categories = DB::select("SELECT types.id as id, types.name as name, SUM(expenses.price) as price, color_code as color_code
                                    FROM types
                                    LEFT JOIN expenses on types.id = expenses.type_id AND expenses.user_id = ? AND MONTH(expenses.date) = ?
                                    WHERE types.id != 99
                                    GROUP BY types.id
                                    ORDER BY types.id asc", [auth()->user()->id, $thisMonth]);

        if (empty($budgetQuery)) {
            $budgetThisMonth = 0;
        }
        else {
            $budgetThisMonth = $budgetQuery[0]->amount + 0;
        }
        
        $spentThisMonthQuery = DB::select("SELECT SUM(price) as amount
                                FROM expenses
                                WHERE user_id = ? AND MONTH(expenses.date) = ? AND YEAR(expenses.date) = ?", [auth()->user()->id, $thisMonth, $thisYear]);

        $saved = number_format((float)$budgetThisMonth - $spentThisMonthQuery[0]->amount, 2, '.', '');
        $currency =  User::where('id', auth()->user()->id)->value('currency');

        return view('budget', compact('monthName', 'budgetThisMonth', 'categoryBudgetQuery', 'saved', 'categories', 'currency'));
    }

    public function editBudget(Request $request)
    {   
        $date = Carbon::now();
        $monthName = $date->format('F');
        $id = $request->id;
        $name = $request->name;
        if ($id == 99) {
            $title = "Budget for $monthName";
        }
        else {
            $title = "Budget for $name";
        }
        $thisMonth = $this->getThisMonth();
        $thisYear = $this->getThisYear();
        $budgetQuery = DB::select("SELECT amount 
                                    FROM budgets
                                    WHERE MONTH(budgets.date) = ? AND YEAR(budgets.date) = ? AND budgets.user_id = ? AND budgets.type_id = ?", [$thisMonth, $thisYear, auth()->user()->id, $id]);

        if (empty($budgetQuery)) {
            $budgetThisMonth = 0;
        }
        else {
            $budgetThisMonth = $budgetQuery[0]->amount + 0;
        }                            

        return view('add-budget', compact('title', 'id', 'budgetThisMonth'));
    }

    public function storeBudget(Request $request)
    {
        $this->validate($request, [
            'amount'      => 'required|numeric|min:0.01'
        ]);
        
        $thisMonth = $this->getThisMonth();
        $thisYear = $this->getThisYear();
        $budgetQuery = DB::select("SELECT amount 
                                    FROM budgets
                                    WHERE MONTH(budgets.date) = ? AND YEAR(budgets.date) = ? AND budgets.type_id = ? AND budgets.user_id = ?", [$thisMonth, $thisYear, $request->type_id, auth()->user()->id]);
        if (empty($budgetQuery)) {
            $budget = new Budget();
            $budget->user_id = auth()->user()->id;
            $budget->type_id = $request->type_id;
            $budget->amount = $request->amount;
            $budget->date = $date = Carbon::now();
            $budget->save();
        }
        else {
            $query = DB::update("UPDATE budgets 
                                SET amount = ? 
                                WHERE MONTH(date) = ? AND YEAR(date) = ? AND type_id = ? AND user_id = ?", [$request->amount, $thisMonth, $thisYear, $request->type_id, auth()->user()->id]);
        }
        
        return redirect('/budget');

    }
}
