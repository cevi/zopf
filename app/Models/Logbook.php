<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    //
    protected $fillable = [
        'comments', 'user_id', 'action_id', 'cut', 'quantity', 'wann'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function action(){
        return $this->belongsTo('App\Models\Action');
    }
}
