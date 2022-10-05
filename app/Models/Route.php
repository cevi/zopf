<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    //
    protected $fillable = [
    'name',
    'action_id',
    'route_status_id',
    'user_id',
    'route_type_id',
    'sequenceDone',
    'photo'
    ];

    protected $casts = [
        'sequenceDone' => 'boolean'
    ];

    public function action(){
        return $this->belongsTo('App\Models\Action');
    }

    public function route_status(){
        return $this->belongsTo('App\Models\RouteStatus');
    }

    public function route_type(){
        return $this->belongsTo('App\Models\RouteType');
    }

    public function zopf_count(){
        return Order::where('route_id',$this->id)->sum('quantity');
    }

    public function zopf_open_count(){
        return Order::where('route_id',$this->id)->where('order_status_id', '<=', config('status.order_unterwegs'))->sum('quantity');
    }

    public function route_done_percent(){
        $open = $this->zopf_open_count();
        $all = $this->zopf_count();
        if($all>0){
            return ($all-$open)/$all * 100;
        }
        else{
            return 0;
        }
    }

    public function order_count(){
        return Order::where('route_id',$this->id)->count();
    }

    public function orders(){
        return $this->hasMany('App\Models\Order')->orderBy('sequence');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
