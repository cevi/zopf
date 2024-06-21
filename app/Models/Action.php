<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Action extends Model
{
    use Notifiable;

    //
    public static function boot()
    {
        parent::boot();

        // here assign this group to a global group with global default role
        self::created(function (Action $action) {
            // get the admin user and assign roles/permissions on new team model
            ActionUser::create([
                'user_id' => config('status.Administrator'),
                'role_id' => config('status.role_administrator'),
                'action_id' => $action['id'],
            ]);
        });
    }

    protected $fillable = [
        'name', 'group_id', 'year', 'action_status_id', 'address_id', 'APIKey', 'SmartsuppToken', 'global', 'demo', 'user_id', 'total_amount',
    ];

    protected $casts = [
        'global' => 'boolean',
        'demo' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function action_status()
    {
        return $this->belongsTo('App\Models\ActionStatus');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public function addresses()
    {
        return $this->belongsToMany('App\Models\Address', 'orders');
    }

    public function center()
    {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }

    public function allUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', 'action_users')->where('action_users.role_id', '<>', config('status.role_administrator'))->orderBy('username');
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable');
    }
}
