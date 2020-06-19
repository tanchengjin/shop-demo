<?php


namespace App\Admin\Controllers;


use App\Category;
use App\Http\Controllers\Controller;
use App\Jobs\SyncProductToES;
use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

abstract class CommonProductController extends AdminController
{
    abstract function getProductType();

    abstract protected function customForm(Form $form);

    abstract protected function customGrid(Grid $grid);

    public function index(Content $content)
    {
        return $content->header(Product::$typeMap[$this->getProductType()] . '列表')->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content->header('修改' . Product::$typeMap[$this->getProductType()])->body($this->form(true)->edit($id));
    }

    public function create(Content $content)
    {
        return $content->header('创建' . Product::$typeMap[$this->getProductType()])->body($this->form());
    }

    protected function grid()
    {
        $grid = new Grid(new Product());
        $grid->model()->where('type', $this->getProductType());

        $this->customGrid($grid);

        return $grid;
    }

    protected function form($isEdit = false)
    {
        $form = new Form(new Product());

        $form->hidden('type')->value($this->getProductType());

        $form->select('category_id', __('分类'))->options(function ($category_id) {
            $category = Category::query()->find($category_id);
            if ($category) {
                return [$category->id => $category->full_name];
            }
        })->ajax('/admin/api/categories?is_directory=0');

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

        $this->customForm($form);

        $form->hasMany('sku', 'sku',function (Form\NestedForm $form) {
            $form->text('title', '标题')->required();
            $form->decimal('price', '价格')->required();
            $form->number('stock', '库存')->required()->default(1);
            $form->textarea('description', '描述');
        });

        $form->hasMany('properties', '属性', function (Form\NestedForm $form) {
            $form->text('name', '属性名')->required();
            $form->text('value', '属性值')->required();
        });
        $form->saving(function (Form $form) {
            $form->model()->min_price = collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
            $form->model()->max_price = collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME, 0)->max('price') ?: 0;
        });

        $form->saved(function (Form $form) {
            $product = $form->model();
            dispatch(new SyncProductToES($product));
        });

        return $form;
    }
}
