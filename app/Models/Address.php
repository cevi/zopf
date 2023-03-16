<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Address extends Model
{
    //
    use SearchableTrait;
    use HasFactory;

    protected $fillable = [
        'name', 'firstname', 'street', 'city_id', 'group_id', 'lat', 'lng', 'plz', 'city', 'center',
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

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}
