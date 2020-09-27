<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    //
    protected $fillable = [
    'name',
    'action_id',
    'route_status_id',
    'user_id',
    'route_type_id'
    ];

        
    public function route_status(){
        return $this->belongsTo('App\RouteStatus');
    }

    public function route_type(){
        return $this->belongsTo('App\RouteType');
    }

    public function zopf_count(){
        return Order::where('route_id',$this->id)->count();
    }

    public function zopf_open_count(){
        return Order::where('route_id',$this->id)->where('order_status_id', '<=', config('status.order_unterwegs'))->count();
    }

    public function route_done_percent(){
        $done = Order::where('route_id',$this->id)->where('order_status_id', '>', config('status.order_unterwegs'))->count();
        $all = Order::where('route_id',$this->id)->count();
        return $done/$all * 100;
    }

    public function order_count(){
        return Order::where('route_id',$this->id)->sum('quantity');
    }

    public function orders(){
        return $this->hasMany('App\Order');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

}
