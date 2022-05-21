<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Report::query()->delete();

        $period = \Carbon\CarbonPeriod::create('2022-01-01', '2022-05-21');

        foreach($period as $row) {
            $name = $row->format('d-m-Y');
            $dateStr = $row->format('Y-m-d');

            $firstPrefixIncomeRange = (string)mt_rand(150, 1500);

            Report::create([
                'name' => 'laporan ('.$name.')',
                'total_income' => $firstPrefixIncomeRange.'00',
                'tanggal' => $dateStr
            ]);
        }
    }
}
