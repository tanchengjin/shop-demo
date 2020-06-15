<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\ReviewRequest;
use App\Order;
use App\OrderItem;
use App\Services\OrderService;
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

    #订单确认
    public function payment(Order $order)
    {
        $order->load(['item', 'item.product', 'item.sku']);
        $userCoupons = Auth::user()->userCoupon()
            ->with(['coupon' => function ($query) {
                $query->whereDate('start_time', '<=', Carbon::now())
                    ->whereDate('end_time', '>=', Carbon::now());
            }])->get();

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
