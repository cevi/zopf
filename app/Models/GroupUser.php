<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'user_id', 'role_id'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function group(){
        return $this->belongsTo('App\Models\Group');
    }

    public function role(){
        return $this->belongsTo('App\Models\Role');
    }
}
