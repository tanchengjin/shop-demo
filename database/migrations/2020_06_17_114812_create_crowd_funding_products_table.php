<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrowdFundingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdfunding_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->on('products')->references('id')->onDelete('cascade');
            $table->decimal('target_amount',10,2);
            $table->decimal('current_amount',10,2)->default(0);
            $table->unsignedInteger('user_count')->default(0);
            $table->dateTime('end_at');
            $table->string('status')->default(\App\CrowdfundingProduct::STATUS_FUNDING);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crowd_funding_products');
    }
}
