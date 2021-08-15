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

    public function action(){
        return $this->belongsTo('App\Action');
    }
        
    public function route_status(){
        return $this->belongsTo('App\RouteStatus');
    }

    public function route_type(){
        return $this->belongsTo('App\RouteType');
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
        return $this->hasMany('App\Order')->orderBy('sequence');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

}
