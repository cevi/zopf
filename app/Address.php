<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Address extends Model
{
    //
    use SearchableTrait;

    protected $fillable = [
    'name', 'firstname', 'street', 'city_id', 'group_id', 'lat', 'lng', 'plz', 'city'
    ];

    
    protected $searchable = [
        'columns' => [
            'addresses.name' => 10,
            'addresses.firstname' => 5, 
            'addresses.street' => 5,
            'cities.name' => 5,
            'cities.plz' => 5,
        ],
        'joins' => [
            'cities' => ['cities.id', 'addresses.city_id'],
        ],
    ];

    public function city(){
        return $this->belongsTo('App\City');
    } 
    
    public function group(){
        return $this->belongsTo('App\Group');
    }  
}
