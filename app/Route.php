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
    'user_id'
    ];

        
    public function route_status(){
        return $this->belongsTo('App\RouteStatus');
    }

    public function zopf_count(){
        return Order::where('route_id',$this->id)->count();
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
