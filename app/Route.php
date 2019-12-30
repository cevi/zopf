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
}
