<?php

namespace App\Admin\Controllers;

use App\CrowdfundingProduct;
use App\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class crowdfundingProductController extends CommonProductController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Product';

    protected function customForm(Form $form)
    {
        $form->date('crowdfunding.end_at', '众筹结束时间')->rules('required');
        $form->decimal('crowdfunding.target_amount', '众筹目标金额')->rules('required');
    }

    function getProductType()
    {
        return Product::TYPE_CROWDFUNDING;
    }

    protected function customGrid(Grid $grid)
    {
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->batchActions(function ($batch) {
            $batch->disableDelete();
        });
        $grid->column('id', 'id');
        $grid->column('title', '商品名称');
        $grid->column('price', '商品价格')->display(function () {
            return $this->min_price . '~' . $this->max_price;
        });
        $grid->column('on_sale', '状态')->display(function ($value) {
            return $value ? '已上架' : '已下架';
        });
        $grid->column('crowdfunding.end_at', '众筹结束时间');
        $grid->column('crowdfunding.current_amount', '当前金额');
        $grid->column('crowdfunding.target_amount', '众筹目标金额');
        $grid->column('crowdfunding.status', '状态')->display(function ($value) {
            return CrowdfundingProduct::$statusMap[$value];
        });

        return $grid;
    }
}
