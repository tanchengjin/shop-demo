<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    protected $fillable = [
        'total_amount', 'remark', 'address', 'closed','paid_at',
        'payment_method','payment_no','ship_status','extra',
        'refund_status','refund_no'
    ];
    protected $casts = [
        'address' => 'json',
        'extra'=>'json'
    ];
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_RECEIVED = 'received';
    const SHIP_STATUS_DELIVERED = 'delivered';

    public static $shipMap = [
        self::SHIP_STATUS_RECEIVED => '已收货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_PENDING => '未发货'
    ];
    public static $refundMap = [
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_APPLIED => '已发起退款请求',
        self::REFUND_STATUS_PROCESSING => '退款处理中',
        self::REFUND_STATUS_FAILED => '退款已拒绝',
        self::REFUND_STATUS_PENDING => '未发起退款',
    ];
    const ALIPAY='alipay';
    const WECHATPAY='wechat';
    public static $paymentMap=[
        self::ALIPAY=>'支付宝',
        self::WECHATPAY=>'微信',
    ];
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        self::creating(function ($model) {
            if (!$model->no) {
                $model->no = static::createOrderNo();
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    protected static function createOrderNo()
    {
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_pad(random_int(000000, 999999), 6, 0);
            if (!self::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function item()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public static function createRefundNo()
    {
        do{
            $no=Uuid::uuid4()->getHex();
        }while(self::query()->where('refund_no',$no)->exists());
        return $no;
    }
}
