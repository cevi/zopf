<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    public static function boot()
    {
        parent::boot();

        // here assign this group to a global group with global default role
        self::created(function (Group $group) {
            // get the admin user and assign roles/permissions on new team model
            GroupUser::create([
                'user_id' => config('status.Administrator'),
                'role_id' => config('status.role_administrator'),
                'group_id' => $group['id'],
            ]);
        });
    }

    protected $fillable = [
        'name', 'user_id', 'global', 'demo',
    ];

    protected $casts = [
        'global' => 'boolean',
        'demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function allUsers()
    {
        return $this->belongsToMany('App\Models\User', 'group_users')->where('group_users.role_id', '<>', config('status.role_administrator'));
    }

    public function actions()
    {
        return $this->hasMany('App\Models\Action');
    }
}
