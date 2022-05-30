<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportDetail;

class SeederController extends Controller
{
    public function reportSeeder() {
        $lastTime = Report::orderByDesc('tanggal')->first()->tanggal;

        $period = \Carbon\CarbonPeriod::create($lastTime, date('Y-m-d'));

        $minIncome = '65000';
        $maxIncome = '200000';

        $products = Product::all();

        foreach($period as $row) {
            $name = $row->format('d-m-Y');
            $dateStr = $row->format('Y-m-d');
            if($dateStr === $lastTime) {
                continue;
            }


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

        return back();
    }
    public function checkSeeder() {
        return dd(Product::doesntHave('solds')->get());
    }
    public function productSeeder() {
        Product::query()->delete();


        $product_list = \file_get_contents("C:\Users\alitelsit1\Documents\apps\laporan_kafe_asmara\produk_kafe_asmara.json");
        // $product_list = preg_replace('/\s+/', '',$product_list);
        $product_list = str_replace('ï»¿', '',utf8_encode($product_list));
        $products = \json_decode($product_list,true);
        $products = $products['items'];
        $products = array_map(function($item) {
            if(isset($item['HARGA'])) {
                $price = \str_replace('Rp.', '', $item['HARGA']);
                $price = str_replace('.', '', $price);
                return [
                    'HARGA' => $price,
                    'MENU' => $item['MENU'],
                ];
            }
            return $item;
        }, $products);


        foreach($products as $row) {
            if(isset($row['HARGA'])) {
                Product::create([
                    'name' => $row['MENU'],
                    'price' => $row['HARGA']
                ]);

                echo 'Name: '.$row['MENU'].'<br />';
                echo 'PRICE: '.$row['HARGA'].'<br /><br />';
            }
        }
    }
}
