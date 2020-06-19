<?php

namespace App\Admin\Controllers;

use App\CrowdfundingProduct;
use App\Http\Requests\HandleRefundRequest;
use App\Order;
use App\Product;
use App\Services\OrderService;
use Encore\Admin\Controllers\AdminController;
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

        $grid->disableCreateButton();

        $grid->model()->orderBy('created_at', 'desc');
        $grid->column('no', __('订单号'))->filter('like');
        $grid->column('user.name', __('用户名'));
        $grid->column('total_amount', __('订单金额'))->display(function ($value) {
            if ($value) {
                return '￥' . number_format($value, 2);
            }
        })->filter('range');
        $grid->column('payment_method', __('支付方式'))->display(function ($value) {
            if ($value) {
                return Order::$paymentMap[$value];
            } else {
                return $value;
            }
        });

        $grid->column('created_at', __('创建时间'))->date('Y-m-d H:i')->filter('range', 'date')->sortable();

        $grid->column('reviewed', '评价状态')->display(function ($reviewed) {
            return $reviewed ? '已评价' : '未评价';
        });
        $grid->column('id', '订单状态')->display(function ($id) {
            $order = Order::query()->find($id);
            if ($order) {
                if ($order->paid_at) {
                    if ($order->refund_status !== 'pending') {
                        return "<span class='label label-danger'>" . Order::$refundMap[$order->refund_status] . "</span>";
                    }

                    if ($order->ship_status === 'pending') {
                        return "<span class='label label-danger'>" . Order::$shipMap[$order->ship_status] . "</span>";
                    } else {
                        return "<span class='label label-info'>" . Order::$shipMap[$order->ship_status] . "</span>";
                    }

                } elseif ($order->closed) {
                    return "<span class='label label-warning'>已关闭</span>";
                } else {
                    return '待付款';
                }
            }
            return '未知错误';
        });

        $grid->actions(function ($action) {
            $action->disableDelete();
            $action->disableEdit();
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        if ($order = Order::query()->find($id)) {
            return $content->header('订单信息')->body(view('admin.orders.show', compact('order')));
        }
    }

    public function ship(Order $order, Request $request)
    {
        if (!$order->paid_at) {
            throw new \Exception('订单未支付');
        }

        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new \Exception('订单状态不正确');
        }

        if ($order->type === Product::TYPE_CROWDFUNDING && $order->crowdfunding_status !== CrowdfundingProduct::STATUS_SUCCESS) {
            throw new \Exception('众筹订单必须众筹成功后才可以发货');
        }
        $data = $this->validate($request, [
            'ship_company' => ['required'],
            'ship_no' => ['required']
        ], [], [
            'ship_company' => '物流公司',
            'ship_no' => '物流单号'
        ]);
        $extra = $order->extra ?: null;
        $extra['ship_company'] = $data['ship_company'];
        $extra['ship_no'] = $data['ship_no'];
        $order->update([
            'extra' => $extra,
            'ship_status' => Order::SHIP_STATUS_DELIVERED
        ]);
        return redirect()->back();
    }

    public function handleRefund(Order $order, HandleRefundRequest $request, OrderService $orderService)
    {
        if (!$order->paid_at) {
            throw new \Exception('订单未支付');
        }
        if ($order->refund_status != Order::REFUND_STATUS_APPLIED) {
            throw new \Exception('订单状态不正确');
        }
        if ($request->input('agree')) {
            $extra = $order->extra ?: [];
            if (!empty($extra) && isset($extra['refuse_refund_reason'])) {
                unset($extra['refuse_refund_reason']);
            }
            $order->update([
                'extra' => $extra
            ]);
            $orderService->handleRefund($order);
        } else {
            $extra = $order->extra ?: [];
            $extra['refuse_refund_reason'] = $request->input('reason');
            $order->update([
                'extra' => $extra,
                'refund_status' => Order::REFUND_STATUS_PENDING
            ]);
        }

        return [];


    }

    /**
     * @param Order $order
     * @throws \Exception
     * @deprecated
     */
    private function _handleRefund(Order $order)
    {
        switch ($order->payment_method) {
            case 'alipay':
                $no = Order::createRefundNo();
                $res = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $no
                ]);

                if ($res->sub_code) {
                    $extra = $order->extra ?: [];
                    $extra['alipay_refund_fail_code'] = $res->sub_code;
                    $order->update([
                        'refund_no' => $no,
                        'extra' => $extra,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                    ]);
                } else {
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_no' => $no
                    ]);
                }
                break;
            case 'wechat':
                //todo
                break;
            default:
                throw new \Exception('退款异常');
                break;
        }
    }
}
