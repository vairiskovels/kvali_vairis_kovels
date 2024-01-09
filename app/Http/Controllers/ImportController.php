<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\ImportToken;
use App\Models\Type;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Auth;

class ImportController extends Controller
{

    private $cliendId;
    private $clientSecret;

    public function __construct($cliendId = null, $clientSecret = null)
    {
        $this->cliendId = $cliendId;
        $this->clientSecret = $clientSecret;
    }

    public function setMyVariable() {
        // Saglabā datus veiksmīgiem GoCardless API pieprasījumiem 
        $this->cliendId = env('CLIENT_ID');
        $this->clientSecret = env('CLIENT_SECRET');
    }

    public function getClientId() {
        return $this->cliendId;
    }
    public function getclientSecret() {
        return $this->clientSecret;
    }

    public function afterBankSelection(Request $request) {

        // Iegūst bankas izvēlēto identifikatoru
        $institution_id = $request->input("iban");

        // Pārbauda vai lietotājam eksistē žetons "requisition_id"
        $reqExists = ImportToken::where('user_id', auth()->user()->id)->where('institution_id', $institution_id)->value('requisition_id');
        // Ja eksistē, tad pāriet pie kontu izvēlnes skata
        if (!is_null($reqExists)) {
            return redirect('/import/account-selection?institution_id='.$institution_id);
        }
        // Ja neeksistē, tad
        else {
            // iegūst nepieciešamos datus
            $accessToken = User::where('id', auth()->user()->id)->value('access_token');
            $createLink = $this->buildLink($accessToken, $institution_id);
            $requisition = $createLink["id"];
            $this->storeRequisition($requisition, $institution_id);
            $link = $createLink["link"];
            // pāradresē lietotāju uz internetbankas autentifikācijas portālu
            return redirect()->away($link);
        }
    }

    public function getAccessToken() {
        // Pieprasa GoCardless žetonu "access_token"
        $tokenUrl = 'https://bankaccountdata.gocardless.com/api/v2/token/new/';

        $requestBody = [
            'secret_id'     => $this->getClientId(),
            'secret_key'    => $this->getClientSecret(),
        ];

        $options = [
            'headers' => [
                'accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'verify' => false, // Disabled as there are some problems with my pc, this should be true.
        ];

        $response = Http::withOptions($options)->post($tokenUrl, $requestBody);

        return $response->json();
    }

    public function storeAccessToken($token, $seconds) {
        $validUntil = Carbon::now()->addSeconds($seconds);
        DB::insert('UPDATE users SET access_token = ?, access_valid_until = ? WHERE id = ?', [$token, $validUntil, auth()->user()->id]);
    }

    public function updateAccessToken($token, $seconds) {
        $validUntil = Carbon::now()->addSeconds($seconds);
        User::where('id', auth()->user()->id)->update([
            'access_token' => $token,
            'access_valid_until' => $validUntil
        ]);
    }

    public function storeRequisition($token, $institution_id) {
        $validUntil = Carbon::now()->addDays(90);

        $requisition = new ImportToken();
        $requisition->user_id = auth()->user()->id;
        $requisition->requisition_id = $token;
        $requisition->req_valid_until = $validUntil;
        $requisition->institution_id = $institution_id;
        $requisition->save();
    }

    public function getBanks($accessToken) {
        // Pieprasa GoCardless pieejamās bankas
        $tokenUrl = 'https://bankaccountdata.gocardless.com/api/v2/institutions/?country=lv';

        $options = [
            'headers' => [
                'accept'        => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ],
            'verify' => false,
        ];

        $response = Http::withOptions($options)->get($tokenUrl);

        return $response->json();
    }

    public function showBanks() {
        $this->setMyVariable();
        $accessToken = null;

        // Pārbauda vai lietotājam ir iestatīts žetons "access_token"
        $accessExists = User::where('id', auth()->user()->id)->whereNotNull('access_token')->exists();
        if ($accessExists) {
            // Pieprasa žetona derīguma termiņu un esošo datumu un laiku
            $time = User::where('id', auth()->user()->id)->value('access_valid_until');
            $validUntil = Carbon::parse($time);
            $now = Carbon::now();

            // Ja žetona derīguma termiņš nav iztecējis, tad dabū lietotāja žetonu
            if ($now->lt($validUntil)) {
                $accessToken = User::where('id', auth()->user()->id)->value('access_token');
            } else {
                // Citādi pieprasa GoCardless jaunu žetonu un saglabā to
                $accessToken = $this->getAccessToken()["access"];
                $accessExpires = $this->getAccessToken()["access_expires"];
                $this->updateAccessToken($accessToken, $accessExpires);
            }
        } else {
            // Ja žetons neeksistē, tad pieprasa GoCardless jaunu žetonu un saglabā to
            $accessToken = $this->getAccessToken()["access"];
            $accessExpires = $this->getAccessToken()["access_expires"];
            $this->storeAccessToken($accessToken, $accessExpires);
        }
        $banks = $this->getBanks($accessToken);
        return view("select-bank", compact('banks'));
    }

    public function createEUA($accessToken) {
        $euaUrl = 'https://bankaccountdata.gocardless.com/api/v2/agreements/enduser/';

        $requestBody = [
            'institution_id'    => 'SWEDBANK_HABALV22',
        ];

        $options = [
            'headers' => [
                'accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ],
            'verify' => false, 
        ];

        $response = Http::withOptions($options)->post($euaUrl, $requestBody);

        return $response->json();
    }

    public function buildLink($accessToken, $institution_id) {
        // Pieprasa GoCardless žetonu "requisition_id" un internetbankas autentifikācijas saiti
        $requisitionUrl = 'https://bankaccountdata.gocardless.com/api/v2/requisitions/';

        $requestBody = [
            "redirect"          => "http://kvali.test/import/account-selection?institution_id=".$institution_id,
            "institution_id"    => $institution_id,
            "user_language"     => "EN"
        ];

        $options = [
            'headers' => [
                'accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ],
            'verify' => false, 
        ];

        $response = Http::withOptions($options)->post($requisitionUrl, $requestBody);

        return $response->json();
    }

    public function listAccounts($req) {
        // Pieprasa GoCardless lietotāja bankas kontu indentifikatorus
        $listUrl = 'https://bankaccountdata.gocardless.com/api/v2/requisitions/'.$req.'/';
        $accessToken = User::where('id', auth()->user()->id)->value('access_token');
        $options = [
            'headers' => [
                'accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$accessToken
            ],
            'verify' => false, 
        ];

        $response = Http::withOptions($options)->get($listUrl);

        return $response->json();
    }

    public function getAccountDetails($accounts) {
        // Pieprasa GoCardless lietotāja kontu informāciju
        $accountDetails = [];
        foreach ($accounts as $accountId) {
            $url = 'https://bankaccountdata.gocardless.com/api/v2/accounts/'.$accountId.'/details';
            $accessToken = User::where('id', auth()->user()->id)->value('access_token');
            $options = [
                'headers' => [
                    'accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer '.$accessToken
                ],
                'verify' => false, 
            ];
    
            $response = Http::withOptions($options)->get($url)->json();
            array_push($accountDetails, $response);
        }

        return $accountDetails;
    }

    public function getTransactions($accountIds) {
        // Pieprasa GoCardless konta tranzakcijas
        $transactions = [];
        foreach ($accountIds as $accountId) {
            $transactionUrl = 'https://bankaccountdata.gocardless.com/api/v2/accounts/'.$accountId.'/transactions';
            $accessToken = User::where('id', auth()->user()->id)->value('access_token');
            $options = [
                'headers' => [
                    'accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer '.$accessToken
                ],
                'verify' => false, 
            ];
    
            $response = Http::withOptions($options)->get($transactionUrl)->json();
            array_push($transactions, $response);
        }

        return $transactions;
    }

    public function getUniqueTransactions($transactions) {
        // Funkcija pārbauda vai tranzakcija ar unikālu id vēl neeksistē datubāzē
        $uniqueTransactions = [];

        foreach ($transactions as $transaction) {
            $uniqueId = $transaction["internalTransactionId"];
            if (!Expense::where('internal_id', $uniqueId)->exists()) {
                array_push($uniqueTransactions, $transaction);
            }
        }

        return $uniqueTransactions;
    }

    public function sortTransactions($transactions) {
        // Funkcija atgriež tranzakcijas informāciju ar piešķirtu tai tipu
        $sorted = [];
        
        $selfPayoutNames = [
            "Maksājums starp saviem kontiem"
        ]; 
        // Vārdi kādi var būt tranzakcijā, lai noteiktu kāda kategorija tai ir
        $bills = ["/insurance/i", "/bite/i", "/L.A.T./i", "/LATVENERGO/i", "/LATVIJAS GĀZE/i", "/RĪGAS NAMU PĀRVALDNIEKS/i"];
        $groceries = ["/rimi/i", "/lidl/i", "/vynoteka/i", "/tesco/i", "/top-veikals/i", "/maxima/i"];
        $entertainment = ["/spotify/i", "/ticketpoint/i", "/steam/i", "/bars/i", "/apollo/i", "/Boulinga/i"];
        $wellbeing = ["/drogas/i", "/dental/i"];
        $snacks = ["/hesburger/i" , "/mcdonalds/i", "/narvesen/i"];
        $clothes = ["/asos/i", "/aboutyou/i"];
        $transport = ["/citybee/i", "/circle k/i", "/bolt/i", "/neste/i", "/mobilly/i", "/tuul/i"];
        // Tranzakcijas kategorijas identifikators
        $types = [
            "1" => $bills,
            "2" => $groceries,
            "3" => $entertainment,
            "4" => $wellbeing,
            "5" => $snacks,
            "6" => $clothes,
            "7" => $transport
        ];
        // Tranzakcijas kategorijas nosaukums
        $type_names = [
            "1" => "Bills",
            "2" => "Groceries",
            "3" => "Entertainment",
            "4" => "Wellbeing",
            "5" => "Snacks",
            "6" => "Clothes",
            "7" => "Transport",
        ];

        foreach ($transactions as $transaction) {
            // Pārbauda vai bankas tranzakcija nav negatīva (nozīmē, ka tranzakcija tika saņemta) un ka tranzakcija nav maksājums starp saviem kontiem
            if ($transaction["transactionAmount"]["amount"][0] == "-" && !in_array($transaction["remittanceInformationUnstructured"], $selfPayoutNames)) {
                $dict = [];
                $trx_name = "";
                // Nosaka tranzakcijas nosaukumu tādu kādu GoCardless atgriza laukā creditorName un remittanceInformationUnstructured
                if (array_key_exists("creditorName", $transaction)) {
                    $trx_name .= $transaction["creditorName"]." ";
                } 
                if (array_key_exists("remittanceInformationUnstructured", $transaction)) {
                    $trx_name .= $transaction["remittanceInformationUnstructured"];
                } 
                
                // Ja lauki creditorName un remittanceInformationUnstructured netika atgriezti, tad tranzakcijas nosaukums ir -
                if ($trx_name == "") {
                    $trx_name = "-";
                }
                
                // Pārbauda vai tranzakcijas nosaukums nesastāv no vārdiem, kuri indentificētu tranzakcijas tipu
                $dict["type_id"] = 8;
                $dict["type_name"] = "Other";
                foreach ($types as $id => $names) {
                    foreach($names as $name) {
                        // Ja ar regex palīdzību atrod sakritību, tad piesaista tranzakcijai tipu
                        if (preg_match($name, $trx_name)) {
                            $dict["type_id"] = $id;
                            $dict["type_name"] = $type_names[$id];
                            break;
                        }
                    }
                }

                // Nosaka tranzakcijas informācij - nosaukumu, cenu, datumu un unikālu identifikatoru
                $dict["name"] = $trx_name;
                $dict["amount"] = substr($transaction["transactionAmount"]["amount"], 1);
                $dict["currency"] = $transaction["transactionAmount"]["currency"];
                $dict["date"] = $transaction["valueDate"];
                $dict["id"] = $transaction["internalTransactionId"];
                array_push($sorted, $dict);
            }
        }

        return $sorted;
    }

    public function accountSelection(Request $request) {
        // Funkcija atgriež lietotāja kontu informāciju - identifikatoru, iban, vārdu
        $institution_id = $request->institution_id;
        $req = ImportToken::where('user_id', auth()->user()->id)->where('institution_id', $institution_id)->value('requisition_id');
        $accounts = $this->listAccounts($req)["accounts"];
        $details = $this->getAccountDetails($accounts);
        $accountDetails = [];

        $len = count($accounts);

        // Apvieno konta identifikatoru ar pārējo informāciju
        for ($i = 0; $i < $len; $i++) {
            $name = null;
            $dict = [];
            if (array_key_exists('name', $details[$i]["account"])) {
                $name = $details[$i]["account"]["name"];
            } else {
                $name = "-";
            }
            $dict["id"] = $accounts[$i];
            $dict["iban"] = $details[$i]["account"]["iban"];
            $dict["name"] = $name;
            array_push($accountDetails, $dict);
        }

        return view('select-accounts', ['accounts' => $accountDetails]);
    }

    public function transactionSelection(Request $request) {
        // Funkcija atgriež unikālas tranzakcijas kopā ar to informāciju
        $this->validate($request, [
            'accountIds' => 'required|array|min:1',
        ],
        [
            'accountIds.min' => 'At least one account must be selected.',
        ]);

        $accountIds = $request->input('accountIds', []);
        $transactions = [];
        try {
            // Pieprasa visas konta tranzakcijas
            $allTransactions = $this->getTransactions($accountIds);
            foreach ($allTransactions as $trxs) {
                foreach ($trxs["transactions"]["booked"] as $trx) {
                    // Pievieno sarakstam tikai tās tranzakcijas, kuras ir apstiprinātas
                    array_push($transactions, $trx);
                }
            }
        } catch (\Exception $e) {
            return redirect('/add')->with('error', "Something went wrong!");
        }
        // Atstāj tikai tās tranzakcijas, kuras nav reģistrētas datubāzē jeb unikālas
        $uniqueTransactions = $this->getUniqueTransactions($transactions);
        // Sakārto šo tranzakciju informāciju
        $sortedTransactions = $this->sortTransactions($uniqueTransactions);
        $types = Type::select('id', 'name', 'color_code')->whereNotIn('id', ['99'])->get();
        return view('select-transactions', ['transactions' => $sortedTransactions, 'types' => $types]);
    }

    public function storeTransactions(Request $request) {
        // Saglabā lietotāja izvēlētās tranzakcijas
        $names = $request->input('names', []);
        $amounts = $request->input('amounts', []);
        $dates = $request->input('dates', []);
        $types = $request->input('types', []);
        $selectedIds = $request->input('ids', []);

        for ($i = 0; $i < count($selectedIds); $i++) {
            $expense = new Expense();
            $expense->type_id = $types[$i];
            $expense->user_id = auth()->user()->id;
            $expense->name = $names[$i];
            $expense->price = $amounts[$i];
            $expense->date = Carbon::parse($dates[$i]);
            $expense->internal_id = $selectedIds[$i];
            $expense->save();
        }

        return redirect('/add')->with('message', "Bank transactions have succesfully been added to the list.");
    }
}
