<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Restore;
use App\Product;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductController extends AdminController
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
    protected function grid()
    {
        $grid = new Grid(new Product());
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

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('min_price', __('Min price'));
        $show->field('max_price', __('Max price'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('on_sale', __('On sale'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    public function edit($id, Content $content)
    {
        return $content->header('编辑')->body($this->form(true)->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @param bool $edit
     * @return Form
     */
    protected function form($isEdit = false)
    {
        $form = new Form(new Product());


        $form->number('category_id', __('分类'));
        $form->text('title', __('标题'))->required();
        if ($isEdit) {
            $form->cropper('image', __('图片'))->cRatio(350, 350);

        } else {
            $form->cropper('image', __('图片'))->cRatio(350, 350)->required();
        }


        $form->UEditor('description', __('描述'))->required();
        $form->display('sold_count', __('销量'));
        $form->display('review_count', __('评价数量'));
        $form->switch('on_sale', __('状态'))->states([
            'on' => ['value' => 1, 'text' => '上架', 'color=success'],
            'off' => ['value' => 0, 'text' => '下架', 'color=danger']
        ])->default(1);
        $form->hasMany('sku', function (Form\NestedForm $form) {
            $form->text('title', '标题')->required();
            $form->decimal('price', '价格')->required();
            $form->number('stock', '库存')->required()->default(1);
            $form->textarea('description', '描述');
        });
        $form->saving(function (Form $form) {
            $form->model()->min_price = collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
            $form->model()->max_price = collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME, 0)->max('price') ?: 0;
        });

        return $form;
    }

}
