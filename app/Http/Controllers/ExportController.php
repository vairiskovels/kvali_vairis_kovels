<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Expense;
use Carbon\Carbon;
use Validator;
use Auth;

class ExportController extends Controller
{
    
    public function showExport(Request $request) {
        $types = Type::select('id', 'name', 'icon_name', 'color_code')->whereNotIn('id', ['99'])->get();
        return view('export', compact('types'));
    }

    public function exportData(Request $request) {
        $type = $request->type;
        $format = $request->format;
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $chbx = $request->expenseChbx;

        $this->validate($request, [
            'type'      => 'required',
            'format'    => 'required',
            'dateTo'    => 'required_if:chbx,null|date',
            'dateFrom'    => 'required_if:chbx,null|date',
            'chbx'    => 'nullable|boolean'
        ]);
        
        $expenses = $this->getQueryData($type, $dateFrom, $dateTo, $chbx);
        $fileName = $this->getFileName($type, $format);
        if ($format == 'csv') {
            $result = $this->createCSV($fileName, $expenses);
            $csvFilePath = $result['path'];
            $fileName = $result['name'];
            return Response::download($csvFilePath, $fileName)->deleteFileAfterSend();
        }
        elseif ($format == 'tsv') {
            $result = $this->createTSV($expenses, $fileName);
            $tsvData = $result['data'];
            $headers = $result['headers'];
            return Response::make($tsvData, 200, $headers);
        }
        elseif ($format == 'json') {
            $result = $this->createJson($expenses, $fileName);
            $jsonData = $result['data'];
            $headers = $result['headers'];
            return Response::make($jsonData, 200, $headers);
        }
        else {
            return redirect('/export');
        }

    }

    public function getFileName($typeId, $format) {
        $types = [
            '0' =>  'all-',
            '1' =>  'bills-',
            '2' =>  'groceries-',
            '3' =>  'entertainment-',
            '4' =>  'wellbeing-',
            '5' =>  'snacks-',
            '6' =>  'clothes-',
            '7' =>  'transport-',
            '8' =>  'other-',
            '9' =>  'bank-transactions-',
        ];
        $nameStart = 'export-';
        $nameType = $types[$typeId];
        $nameDate = Carbon::now()->format('Y-m-d');
        return $nameStart.$nameType.$nameDate.'.'.$format;
    }

    public function getQueryData($typeId, $dateFrom, $dateTo, $chbx) {
        $expenses = [];
        
        if ($typeId == 0) {
            if ($chbx == 'on') {
                $expenses = DB::select("SELECT expenses.name, price, expenses.date, types.name as type_name
                                        FROM expenses
                                        JOIN types ON expenses.type_id = types.id
                                        WHERE user_id = ?", [auth()->user()->id]);
            }
            else {
                $expenses = DB::select("SELECT expenses.name, price, expenses.date, types.name as type_name
                                    FROM expenses
                                    JOIN types ON expenses.type_id = types.id
                                    WHERE user_id = ? AND expenses.date >= ? AND expenses.date <= ?", [auth()->user()->id, $dateFrom, $dateTo]);
            }
            
        }
        else {
            if ($chbx == 'on') {
                $expenses = DB::select("SELECT expenses.name, price, expenses.date, types.name as type_name
                                        FROM expenses
                                        JOIN types ON expenses.type_id = types.id
                                        WHERE user_id = ? AND type_id = ?", [auth()->user()->id, $typeId]);
            }
            else {
                $expenses = DB::select("SELECT expenses.name, price, expenses.date, types.name as type_name
                                    FROM expenses
                                    JOIN types ON expenses.type_id = types.id
                                    WHERE user_id = ? AND type_id = ? AND expenses.date >= ? AND expenses.date <= ?", [auth()->user()->id, $typeId, $dateFrom, $dateTo]);
            }
        }

        return $expenses;
    }

    public function createCSV($fileName, $expenses) {
        $csvFilePath = storage_path("app/$fileName");
        $csvFile = fopen($csvFilePath, 'w');
        fputcsv($csvFile, ['Type', 'Name', 'Price', 'Date']); 
        foreach ($expenses as $expense) {
            fputcsv($csvFile, [$expense->type_name, $expense->name, $expense->price, $expense->date]);
        }
        fclose($csvFile);
        
        return ['path' => $csvFilePath, 'name' => $fileName];
    }

    public function createJson($expenses, $fileName) {
        $data = [];
        foreach ($expenses as $expense) {
            $row = [
                'type'  => $expense->type_name,
                'name'  => $expense->name,
                'price'  => $expense->price,
                'date'  => $expense->date,
            ];
            $data[] = $row;
        }

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        $headers = [
            'Content-type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return ['data' => $jsonData, 'headers' => $headers];
    }

    public function createTSV($expenses, $fileName) {
        
        $data = [['Type', 'Name', 'Price', 'Date']];
        foreach ($expenses as $expense) {
            $array = [];
            array_push($array, $expense->type_name);
            array_push($array, $expense->name);
            array_push($array, $expense->price);
            array_push($array, $expense->date);
            array_push($data, $array);
        }
        
        $tsvData = $this->convertToTsv($data);

        $headers = [
            'Content-type' => 'text/tab-separated-values',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return ['data' => $tsvData, 'headers' => $headers];
    }

    private function convertToTsv($data) {
        $output = '';

        foreach ($data as $row) {
            $output .= implode("\t", $row) . PHP_EOL;
        }

        return $output;
    }
}
