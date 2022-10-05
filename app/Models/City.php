<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class City extends Model
{
    //
    use SearchableTrait;

    protected $fillable = [
    'name', 'plz'
    ];

    public function addresses(){
        return $this->hasMany('App\Models\Address');
    }


    protected $searchable = [
        'columns' => [
            'name' => 1,
            'plz' => 1,
        ]
    ];
}
