<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('test');
        $this->createProduct();
    }

    private function createProduct() {
        Product::query()->delete();

        $product_list = \file_get_contents("/var/www/html/laporan_kafe_asmara/produk_kafe_asmara.json");
        $products = \json_decode($product_list, true);

        Log::info('test');

        // foreach($products as $row) {
        //     Product::create([
        //         'name' => $row['MENU'],
        //         'price' => $row['HARGA']
        //     ]);
        // }
    }
}
