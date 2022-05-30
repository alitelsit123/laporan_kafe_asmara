<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Product;
use App\Models\Report;
use App\Models\ReportDetail;
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
        ReportDetail::query()->delete();
        Report::query()->delete();

        $period = \Carbon\CarbonPeriod::create('2022-01-01', date('Y-m-d'));

        $minIncome = '65000';
        $maxIncome = '200000';

        $products = Product::all();

        foreach($period as $row) {
            $name = $row->format('d-m-Y');
            $dateStr = $row->format('Y-m-d');
            $firstPrefixIncomeRange = (string)mt_rand(150, 1500);
            $report = Report::create([
                'name' => 'laporan ('.$name.')',
                // 'total_income' => $firstPrefixIncomeRange.'00',
                'tanggal' => $dateStr
            ]);

            print('Report '.$report->name.'\n');

            $totalProduct = \mt_rand(3,8);
            $detailProduct = $products->shuffle()->take($totalProduct);
            $detailProduct = $detailProduct->values();
            $detailItems = $detailProduct->map(function($rowProduct) {
                $quantity = \mt_rand(1, 5);
                $subTotal = $rowProduct->price * $quantity;
                return [
                    'sub_total' => $subTotal,
                    'quantity' => $quantity,
                    'product_id' => $rowProduct->id
                ];
            });

            $report->details()->createMany($detailItems);

            $report->total_income = $report->details()->sum('sub_total');
            $report->save();

            print('Total Item '.$report->details->sum('quantity').'\n');
            print('Total Income '.$report->total_income.'\n\n');
        }

        // $prods = Product::doesntHave('solds')->get();
        // foreach($period as $row) {
        //     $name = $row->format('d-m-Y');
        //     $dateStr = $row->format('Y-m-d');
        //     $firstPrefixIncomeRange = (string)mt_rand(150, 1500);
        //     $report = Report::create([
        //         'name' => 'laporan ('.$name.')',
        //         // 'total_income' => $firstPrefixIncomeRange.'00',
        //         'tanggal' => $dateStr
        //     ]);

        //     print('Report '.$report->name.'\n');

        //     $totalProduct = \mt_rand(3,5);
        //     $detailProduct = $prods;
        //     $detailItems = $detailProduct->map(function($rowProduct) {
        //         $quantity = \mt_rand(1, 5);
        //         $subTotal = $rowProduct->price * $quantity;
        //         return [
        //             'sub_total' => $subTotal,
        //             'quantity' => $quantity,
        //             'product_id' => $rowProduct->id
        //         ];
        //     });

        //     $report->details()->createMany($detailItems);
        //     // foreach($detailProduct as $rowProduct) {
        //     //     $quantity = \mt_rand(1, 5);
        //     //     $subTotal = $rowProduct->price * $quantity;
        //     //     $report->details()->create([
        //     //         'sub_total' => $subTotal,
        //     //         'quantity' => $quantity,
        //     //         'product_id' => $rowProduct->id
        //     //     ]);
        //     // }

        //     $report->total_income = $report->details()->sum('sub_total');
        //     $report->save();

        //     print('Total Item '.$report->details->sum('quantity').'\n');
        //     print('Total Income '.$report->total_income.'\n\n');
        // }
    }

    
}
