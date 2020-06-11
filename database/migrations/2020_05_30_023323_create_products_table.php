<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->on('categories')->references('id')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->decimal('min_price',10,2);
            $table->decimal('max_price',10,2);
            $table->unsignedBigInteger('sold_count')->default(0);
            $table->unsignedBigInteger('review_count')->default(0);
            $table->boolean('on_sale')->default(1);
            $table->float('rating')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
