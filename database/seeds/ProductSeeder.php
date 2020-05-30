<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::transaction(function(){
            $products=factory(\App\Product::class,100)->create();

            foreach($products as $product){
                $skus=factory(\App\ProductSku::class,3)->create([
                    'product_id'=>$product->id
                ]);

                $product->min_price=collect($skus)->min('price');
                $product->max_price=collect($skus)->max('price');
                $product->save();
            }
        });
    }
}
