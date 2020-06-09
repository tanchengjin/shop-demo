<?php

namespace App\Admin\Controllers;

use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
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
        $grid->model()->orderBy('id','desc');
        $grid->column('id', __('Id'));
        $grid->column('category_id', __('Category id'));
        $grid->column('title', __('商品名'));
        $grid->column('image', __('图片'));
        $grid->column('min_price', __('最低价'))->display(function($value){
            return '￥'.number_format($value,2);
        })->sortable();
        $grid->column('max_price', __('最高价'))->display(function($value){
            return '￥'.number_format($value,2);
        })->sortable();
        $grid->column('sold_count', __('销量'))->display(function($value){
            if($value == 0){
                return '暂无';
            }else{
                return $value;
            }
        })->sortable();

        $grid->column('review_count', __('评价数'))->display(function($value){
            if($value == 0){
                return '暂无';
            }
            return $value;
        })->sortable();
        $grid->column('on_sale', __('状态'))->display(function($value){
            if($value == 1){
                return '在售';
            }else{
                return '下架';
            }
        });
        $grid->column('created_at', __('创建时间'))->date('Y-m-d H:i')->sortable();

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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->number('category_id', __('分类'));
        $form->text('title', __('标题'));
        $form->image('image', __('图片'));

        $form->UEditor('description', __('描述'));
        $form->display('sold_count', __('销量'));
        $form->display('review_count', __('评价数量'));
        $form->switch('on_sale', __('状态'))->states([
            'on'=>['value'=>1,'text'=>'上架','color=success'],
            'off'=>['value'=>0,'text'=>'下架','color=danger']
        ])->default(1);
        $form->hasMany('sku',function(Form\NestedForm $form){
            $form->text('title','标题')->required();
            $form->decimal('price','价格')->required();
            $form->number('stock','库存')->required()->default(1);
            $form->textarea('description','描述');
        });

        $form->saving(function(Form $form){
            $form->model()->min_price=collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME,0)->min('price')?:0;
            $form->model()->max_price=collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME,0)->max('price')?:0;
        });

        return $form;
    }

}
