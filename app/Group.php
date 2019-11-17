<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $fillable = [
        'name', 'user_id'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function users(){
        return $this->hasMany('App\User');
    }
}
