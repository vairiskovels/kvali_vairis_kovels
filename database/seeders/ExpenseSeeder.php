<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Expense;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $type_id = [1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8, 8];
        // $user_id = 1;
        // $names = ['Rēķini','Apkure+ūdens','Visi rēķini','Komunālie','Komunālie rēķini', 'Jūnija rēķini', 'Rimi iepirkšanās','Rimi un Maxima','Pārtika','Aprīļa pārtika','Ēdiens', 'Produkti', 'Netflix+youtube','Netflix','Filmas un seriāli','Steam games','Izklaides', 'Izklaide jūnijā', 'Šampūni','Matu krāsošanai','Drogas preces','Zobupastas un pirkumi zobiem','Sejas kopšanai','Matu kopšanai', 'Čipši','Visādi sneki','Dzērieni un čipši','Cepumi','Gardumi','Gardumi jūnijā', 'Drēbes','Krekli','Džinsi','Džemperis Adidas','Cap', 'Cepures', 'Autobusa biļete','Vilciena biļete','Biļete','Transports','Bus', 'Etalons', 'Krūze','Austiņas','Brilles','Smēre','Uzpirksteņi', 'Naglas'];
        // $prices = [71.11, 55.66, 53.64, 53.72, 58.7, 59.2, 31.05, 36.26, 39.33, 29.9, 30.92, 35.32, 20.36, 21.23, 23.16, 22.4, 22.01, 23.1, 19.01, 17.73, 15.97, 16.44, 15.56, 17.45, 12.56, 11.43, 12.8, 10.5, 12.76, 11.67, 10.14, 11.6, 13.27, 9.4, 11.97, 10.3, 9.39, 11.15, 9.98, 7.69, 12.45, 10.3, 7.6, 9.63, 6.6, 8.2, 8.94, 9.15];
        // $dates = ['2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01','2023-05-01', '2023-06-01','2023-07-01','2023-08-01','2023-09-01', '2023-11-01'];
        // for ($i = 0; $i < count($type_id); $i++) {
            //     Expense::create([
                //         'type_id'   =>  $type_id[$i],
                //         'user_id'   =>  $user_id,
                //         'name'      =>  $names[$i],
                //         'price'     =>  $prices[$i],
                //         'date'      =>  Carbon::parse($dates[$i])
                //     ]);
                // }
                
        $user_id = 1;

        $names = [
            ['Rēķini', 'Komunālie rēķini', 'Apkure', 'Rīgas siltums', 'Internets+rēķini', 'Visi rēķini', 'Apkure+ūdens', 'Elektrum+rēķini', 'Latvenergo', 'Rīgas namu pārvaldnieks', 'Rādītāji'],
            ['Rimi preces', 'Rimi', 'Maxima', 'Veikals', 'Ēdiens', 'Pārtika', 'Pārtikas preces', 'Produkti', 'Tirgus', 'Dārzeņi un augļi'],
            ['Netflix', 'YouTube', 'Kino', 'Izklaides', 'Spotify', 'Playstation subscription', 'Teātris', 'Comedy šovs'],
            ['Drogas', 'Šampūns', 'Spa diena', 'Džakuzī', 'Masāžas terapija', 'Pirtiņa', 'Burāšana', 'Grāmata', 'Mutes dobuma tīrīšanai'],
            ['Čipši', 'Sneki', 'Saldumi un gardumi', 'Konfektes no Marokas', 'Popkorns', 'Lulu pica x5', 'Džeks un kola', 'Cepumi', 'Kaste ar saldējumu'],
            ['Bikses', 'Krekli', 'Ņaģene', 'Cepure', 'Džinsu jaka', 'Ziemas mētelis', 'Šalle', 'Ziemas zābaki', 'Zandeles', 'Zeķes 3 pāri', 'Saulesbrilles', 'Cimdi slēpošanai'],
            ['Biļete', 'Autobusa biļete', 'Tramvaja biļete', 'Trolejbusa biļete', 'Brauciens ar jauno vilcienu', 'Autobuss Rīga - Varšava', 'Mīlas vēstule no Rīgas satiksmes'],
            ['Citi pirkumi', 'Nekategorizēts pirkums', 'Kaut kas ko neatceros', 'Piektdienas tēriņi', 'Nezināmas izcelsmes prece', 'Maksājums', 'Jaunas slotiņas behai', 'Eļļa 5w-40', 'Antifrīzs']
        ];

        $prices = [
            [70.0, 130.0],
            [50.0, 100.0],
            [40.0, 50.0],
            [30.0, 40.0],
            [30.0, 50.0],
            [60.0, 100.0],
            [40.0, 80.0],
            [20.0, 40.0]
        ];

        for ($i = 1; $i <= 12; $i++) {
            $month = "";
            if ($i >= 1 && $i <= 9) {
                $month = $month."0".$i;
            } else {
                $month = $month.$i;
            }

            for ($j = 1; $j <= 8; $j++) {
                $index = $j-1;
                $array = $names[$index];
                $price = $prices[$index][0] + mt_rand() / mt_getrandmax() * ($prices[$index][1] - $prices[$index][0]);
                $date = '2023-'.$month.'-01';
                Expense::create([
                    'type_id'   =>  $j,
                    'user_id'   =>  $user_id,
                    'name'      =>  $array[array_rand($array)],
                    'price'     =>  $price,
                    'date'      =>  Carbon::parse($date)
                ]);
            }
        }
        
    }
}
