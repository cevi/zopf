<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'quantity', 'route_id', 'action_id', 'address_id', 'order_status_id', 'sequence', 'pick_up', 'comments'
    ];

    protected $casts = [
        'pick_up' => 'boolean'
    ];

    public function address(){
        return $this->belongsTo('App\Address');
    } 

    public function order_status(){
        return $this->belongsTo('App\OrderStatus');
    } 

    public function route(){
        return $this->belongsTo('App\Route');
    } 

    public function action(){
        return $this->belongsTo('App\Action');
    } 
}
