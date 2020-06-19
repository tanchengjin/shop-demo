<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Http\Requests\crowdfundingOrderRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\SeckillOrderRequest;
use App\Order;
use App\OrderItem;
use App\Product;
use App\ProductSku;
use App\Services\OrderService;
use App\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->order()->orderBy('created_at', 'desc')->paginate(16);
        $orders->load(['item.product', 'item.sku']);
        return view('orders.index', compact('orders'));
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $remark = $request->input('remark') ?: null;
        $items = $request->input('items');
        $address_id = $request->input('address_id');

        return $orderService->store($items, $address_id, $remark);
    }

    #众筹订单入库
    public function crowdfundingStore(crowdfundingOrderRequest $request, OrderService $orderService)
    {
        $sku = ProductSku::find($request->input('sku_id'));
        $address = UserAddress::find($request->input('address_id'));
        $orderService->crowdfundingStore($request->user(), $sku, $address, $request->input('amount'));
    }

    /**
     * 秒杀订单
     * @param SeckillOrderRequest $request
     * @param OrderService $orderService
     * @throws \Exception
     */
    public function seckill(SeckillOrderRequest $request, OrderService $orderService)
    {
        $sku = ProductSku::find($request->input('sku_id'));
        $address = UserAddress::find($request->input('address_id'));
        $user = $request->user();
        $orderService->seckill($user, $sku, $address);
    }

    #订单确认界面
    public function payment(Order $order)
    {
        $order->load(['item', 'item.product', 'item.sku']);
        //普通商品可以使用优惠券
        $userCoupons = [];
        if ($order->type === Product::TYPE_NORMAL) {
            $userCoupons = Auth::user()->userCoupon()
                ->with(['coupon' => function ($query) {
                    $query->whereDate('start_time', '<=', Carbon::now())
                        ->whereDate('end_time', '>=', Carbon::now());
                }])->get();
        }


        return view('orders.payment', compact('order', 'userCoupons'));
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', compact('order'));
    }

    #收货
    public function received(Order $order)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            return;
        }

        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED
        ]);
        return [];
    }

    #退款
    public function refund(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            return;
        }

        if ($order->type === Product::TYPE_CROWDFUNDING) {
            throw new \Exception('众筹订单不支持退款');
        }
        $data = $this->validate($request, [
            'reason' => ['required', 'min:1'],
        ], [], [
            'reason' => '退款理由'
        ]);

        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $data['reason'];
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra
        ]);
        return [];
    }

    #评价
    public function review(Order $order, ReviewRequest $request)
    {
        if (!$order->paid_at) {
            throw new \Exception('该订单未支付');
        }
        if ($order->reviewed) {
            throw new \Exception('该订单已评价');
        }
        $reviews = $request->input('reviews');
        DB::transaction(function () use ($order, $reviews) {
            foreach ($reviews as $review) {
                $item = OrderItem::query()->find($review['id']);
                $item->update([
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                    'rating' => $review['rating']
                ]);
            }
            $order->update([
                'reviewed' => 1
            ]);
        });

        event(new OrderReviewed($order));

        return redirect()->back();
    }

    #评价界面
    public function reviewIndex(Order $order)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new Exception('error');
        }
        $order->load(['item.product', 'item.sku']);
        return view('orders.review', compact('order'));
    }
}
