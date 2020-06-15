<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasMany(ShoppingCart::class,'user_id','id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'user_id','id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class,'user_id','id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class,'product_favorites')
            ->orderBy('id','desc');
    }

    public function userCoupon()
    {
        return $this->hasMany(UserCoupon::class,'user_id','id');
    }
}
