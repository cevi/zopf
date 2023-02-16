<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id', 'user_id', 'role_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }
}
