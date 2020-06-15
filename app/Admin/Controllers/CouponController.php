<?php

namespace App\Admin\Controllers;

use App\Coupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Coupon';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coupon());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('code', __('Code'));
        $grid->column('value', __('则扣'))->display(function ($value) {
            return $this->type === Coupon::TYPE_FIXED ? '￥' . $value : $value . '%';
        });
        $grid->column('min_amount', __('规则'))->display(function ($value) {
            $rule = '';

            if ($value > 0) {
                $rule = '满' . str_replace('.00', '', $value);
            }
            if ($this->type === Coupon::TYPE_PERCENT) {
                return $rule . '优惠' . str_replace('.00', '', $this->value) . '%';
            }
            return $rule . '减' . str_replace('.00', '', $this->value);
        });
        $grid->column('used', __('使用'))->display(function ($value) {
            return $value . '/' . $this->total;
        });
        $grid->column('start_time', __('Start time'));
        $grid->column('end_time', __('End time'));
        $grid->column('enable', __('Enable'));

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
        $show = new Show(Coupon::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('code', __('Code'));
        $show->field('type', __('Type'));
        $show->field('value', __('Value'));
        $show->field('min_amount', __('Min amount'));
        $show->field('total', __('Total'));
        $show->field('used', __('Used'));
        $show->field('start_time', __('Start time'));
        $show->field('end_time', __('End time'));
        $show->field('enable', __('Enable'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Coupon());

        $form->text('name', __('Name'))->required();
        $form->text('code', __('Code'))->rules(function ($form) {
            if ($id=$form->model()->id) {
                return 'nullable|unique:coupons,code,'.$id.',id';
            } else {
                return 'nullable|unique:coupons';
            }
        });

        $form->radio('type', __('Type'))->options(Coupon::$typeMap)->rules('required')->default(Coupon::TYPE_FIXED);
        $form->text('value', __('则扣'))->rules(function ($form) {
            if (request()->input('type') === Coupon::TYPE_PERCENT) {
                return 'required|numeric|between:1,99';
            } else {
                return 'required|numeric|min:0.01';
            }
        });
        $form->decimal('min_amount', __('Min amount'));
        $form->number('total', __('Total'));
        $form->number('used', __('Used'));
        $form->datetime('start_time', __('Start time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('end_time', __('End time'))->default(date('Y-m-d H:i:s'));
        $form->switch('enable', __('Enable'))->default(1);

        $form->saving(function ($form) {
            if (!$form->code) {
                $form->code = Coupon::createCode();
            }
        });
        return $form;
    }
}
