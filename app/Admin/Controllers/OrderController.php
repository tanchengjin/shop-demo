<?php

namespace App\Admin\Controllers;

use App\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class OrderController extends AdminController
{
    use ValidatesRequests;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->model()->orderBy('created_at','desc');
        $grid->column('no', __('订单号'))->filter('like');
        $grid->column('user.name', __('用户名'));
        $grid->column('total_amount', __('订单金额'))->display(function($value){
            if($value){
                return '￥'.number_format($value,2);
            }
        })->filter('range');
        $grid->column('payment_method', __('支付方式'))->display(function($value){
            if($value){
                return Order::$paymentMap[$value];
            }else{
                return $value;
            }
        });
        $grid->column('refund_status', __('退款状态'));
        $grid->column('ship_status', __('物流状态'))->display(function($value){
            return Order::$shipMap[$value];
        });
        $grid->column('closed', __('订单状态'))->display(function($value){
            if($value === 1){
                return '订单已关闭';
            }else{
                return '待付款';
            }
        })->filter([
            1=>'订单关闭',
            0=>'订单未关闭'
        ]);
        $grid->column('created_at', __('创建时间'))->date('Y-m-d H:i')->filter('range','date')->sortable();
        $grid->column('paid_at', __('付款时间'))->display(function($value){
            if(!is_null($value)){
                return date('Y-m-d H:i:s',strtotime($value));
            }else{
                return '待付款';
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('no', __('No'));
        $show->field('user_id', __('User id'));
        $show->field('address', __('Address'));
        $show->field('remark', __('Remark'));
        $show->field('paid_at', __('Paid at'));
        $show->field('total_amount', __('Total amount'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_no', __('Payment no'));
        $show->field('refund_status', __('Refund status'));
        $show->field('refund_no', __('Refund no'));
        $show->field('ship_status', __('Ship status'));
        $show->field('ship_data', __('Ship data'));
        $show->field('extra', __('Extra'));
        $show->field('closed', __('Closed'));
        $show->field('reviewed', __('Reviewed'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    public function show($id, Content $content)
    {
        if($order=Order::query()->find($id)){
            return $content->header('订单信息')->body(view('admin.orders.show',compact('order')));
        }
    }

    public function ship(Order $order,Request $request)
    {
        if(!$order->paid_at){
            throw new \Exception('订单未支付');
        }

        if($order->ship_status !== Order::SHIP_STATUS_PENDING){
            throw new \Exception('订单状态不正确');
        }
        $data=$this->validate($request,[
            'ship_company'=>['required'],
            'ship_no'=>['required']
        ],[],[
            'ship_company'=>'物流公司',
            'ship_no'=>'物流单号'
        ]);
        $extra=$order->extra?:null;
        $extra['ship_company']=$data['ship_company'];
        $extra['ship_no']=$data['ship_no'];
        $order->update([
            'extra'=>$extra,
            'ship_status'=>Order::SHIP_STATUS_DELIVERED
        ]);
        return redirect()->back();
    }
}
