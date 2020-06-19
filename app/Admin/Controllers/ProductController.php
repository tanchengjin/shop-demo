<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Restore;
use App\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class ProductController extends CommonProductController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function customGrid(Grid $grid)
    {
        $grid->filter(function ($filter) {
            $filter->scope('trashed', '回收站')->onlyTrashed();
        });
        $grid->column('image', __('图片'))->image('',90,90);

        $grid->column('categories.name', __('分类'));
        $grid->column('title', __('商品名'));
        $grid->column('min_price', __('最低价'))->display(function ($value) {
            return '￥' . number_format($value, 2);
        })->sortable();
        $grid->column('max_price', __('最高价'))->display(function ($value) {
            return '￥' . number_format($value, 2);
        })->sortable();
        $grid->column('sold_count', __('销量'))->display(function ($value) {
            if ($value == 0) {
                return '暂无';
            } else {
                return $value;
            }
        })->sortable();

        $grid->column('review_count', __('评价数'))->display(function ($value) {
            if ($value == 0) {
                return '暂无';
            }
            return $value;
        })->sortable();
        $grid->column('on_sale', __('状态'))->display(function ($value) {
            if (!is_null($this->deleted_at)) {
                return '已删除';
            }
            if ($value == 1) {
                return '在售';
            } else {
                return '下架';
            }
        });
        $grid->column('created_at', __('创建时间'))->date('Y-m-d H:i')->sortable();
        $grid->actions(function($actions){
            if(request('_scope_') == 'trashed'){
                $actions->add(new Restore());
            }

        });
        return $grid;
    }



    function getProductType()
    {
        return Product::TYPE_NORMAL;
    }

    protected function customForm(Form $form)
    {

    }
}
