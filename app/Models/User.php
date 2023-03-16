<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SearchableTrait;
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'role_id', 'is_active', 'group_id', 'action_id', 'demo', 'email_verified_at', 'password_change_at', 'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $searchable = [
        'columns' => [
            'email' => 1,
        ],
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_users');
    }

    public function actions()
    {
        return $this->belongsToMany('App\Models\Action', 'action_users');
    }

    public function isAdmin()
    {
        if ($this->role) {
            if ($this->role['id'] === config('status.role_administrator') && $this->is_active == 1) {
                return true;
            }
        }

        return false;
    }

    public function isGroupleader()
    {
        if ($this->role) {
            if ($this->role['id'] <= config('status.role_groupleader') && $this->is_active == 1) {
                return true;
            }
        }
        return false;
    }

    public function isActionleader()
    {
        if ($this->role) {
            if ($this->role['id'] <= config('status.role_actionleader') && $this->is_active == 1) {
                return true;
            }
        }

        return false;
    }
}
