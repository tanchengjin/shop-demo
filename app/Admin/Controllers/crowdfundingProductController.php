<?php

namespace App\Admin\Controllers;

use App\Category;
use App\CrowdfundingProduct;
use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class crowdfundingProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->model()->where('type',Product::TYPE_CROWDFUNDING);
        $grid->actions(function($actions){
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->batchActions(function($batch){
            $batch->disableDelete();
        });
        $grid->column('id','id');
        $grid->column('title','商品名称');
        $grid->column('price','商品价格')->display(function(){
            return $this->min_price.'~'.$this->max_price;
        });
        $grid->column('on_sale','状态')->display(function ($value){
            return $value?'已上架':'已下架';
        });
        $grid->column('crowdfundingProduct.end_at','众筹结束时间');
        $grid->column('crowdfundingProduct.current_amount','当前金额');
        $grid->column('crowdfundingProduct.target_amount','众筹目标金额');
        $grid->column('crowdfundingProduct.status','状态')->display(function($value){
            return CrowdfundingProduct::$statusMap[$value];
        });
        return $grid;
    }

    public function edit($id, Content $content)
    {
        return $content->header('众筹商品')->body($this->form()->edit($id));
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
        $show->field('rating', __('Rating'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('type', __('Type'));

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

        $form->hidden('type')->value(Product::TYPE_CROWDFUNDING);

        $form->select('category_id', __('分类'))->options(function ($id) {
            $category = Category::query()->find($id);
            if ($category) {
                return [$category->id => $category->full_name];
            }
        })->ajax('/admin/api/categories?is_directory=0');
        $form->text('title', __('标题'));
        $form->UEditor('description', __('描述'));
        $form->image('image', __('Image'));
        $form->switch('on_sale', __('是否上架'))->states([
            'on' => ['text' => '上架', 'value' => 1],
            'off' => ['text' => '下架', 'value' => 0]
        ])->default(1);
        $form->date('crowdfundingProduct.end_at','众筹结束时间')->rules('required');
        $form->decimal('crowdfundingProduct.target_amount','众筹目标金额')->rules('required');
        $form->hasMany('sku', function (Form\NestedForm $form) {
            $form->text('title','标题')->rules('required');
            $form->text('price','价格')->rules('required');
            $form->number('stock','库存')->rules('required');
            $form->text('description','描述');
        });

        $form->saving(function(Form $form){
            $form->model()->min_price=collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME,0)->min('price');
            $form->model()->max_price=collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME,0)->max('price');
        });
        return $form;
    }
}
