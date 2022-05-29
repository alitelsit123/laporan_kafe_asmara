<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Report;
use App\Models\ReportDetail;

class SeederController extends Controller
{
    public function reportSeeder() {
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

            echo 'Report '.$report->name.'<br />';

            $totalProduct = \mt_rand(3,15);
            $detailProduct = $products->shuffle()->take($totalProduct);
            foreach($detailProduct as $rowProduct) {
                $quantity = \mt_rand(2, 8);
                $subTotal = $rowProduct->price * $quantity;
                $report->details()->create([
                    'sub_total' => $subTotal,
                    'quantity' => $quantity,
                ]);
            }

            echo 'Total Item '.$report->details->sum('quantity').'<br />';
            echo 'Total Income '.$report->total_income.'<br /><br />';
        }
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
