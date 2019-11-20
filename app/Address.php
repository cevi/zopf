<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $fillable = [
    'name', 'firstname', 'street', 'city_id', 'group_id'
    ];

    public function city(){
        return $this->belongsTo('App\City');
    } 
    
    public function group(){
        return $this->belongsTo('App\Group');
    }  
}
