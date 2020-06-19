<?php


namespace App\Admin\Controllers;

#秒杀
use App\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class SeckillController extends CommonProductController
{

    function getProductType()
    {
        return Product::TYPE_SECKILL;
    }

    protected function customForm(Form $form)
    {
        $form->datetime('seckill.start_at', '秒杀开始时间');
        $form->datetime('seckill.end_at', '秒杀结束时间');
    }

    protected function customGrid(Grid $grid)
    {
        $grid->column('seckill.start_at', '秒杀开始时间');
        $grid->column('seckill.end_at', '秒杀结束时间');
    }
}
