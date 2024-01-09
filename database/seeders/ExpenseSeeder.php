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
        $type_id = [1,2,3,4,5,6,7,8];
        $user_id = 1;
        $names = ['Rēķini', 'Rimi iepirkšanās','Netflix+youtube','Zobupasta','Čipši','Drēbes','Autobusa biļete','Krūze'];
        $prices = [71.11, 32.15, 9.99, 4.99, 1.99, 45.99, 2, 5];
        $dates = ['2024-01-03', '2024-01-01','2024-01-01','2024-01-01','2024-01-01', '2024-01-01', '2024-01-04', '2024-01-15'];
        for ($i = 0; $i < count($type_id); $i++) {
                Expense::create([
                        'type_id'   =>  $type_id[$i],
                        'user_id'   =>  $user_id,
                        'name'      =>  $names[$i],
                        'price'     =>  $prices[$i],
                        'date'      =>  Carbon::parse($dates[$i])
                    ]);
                }
                
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
