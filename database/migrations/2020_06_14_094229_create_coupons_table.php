<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            #优惠券类型
            $table->string('type');
            #则扣值
            $table->decimal('value');
            #最低订单金额
            $table->decimal('min_amount',10,2);
            #优惠券数量
            $table->unsignedBigInteger('total');
            #已发放多少张
            $table->unsignedBigInteger('used')->default(0);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('enable')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
