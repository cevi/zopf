<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'role_id', 'is_active', 'group_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function group(){
        return $this->belongsTo('App\Group');
    }

    public function isAdmin(){
        if($this->role['is_admin'] == 1 && $this->is_active == 1){
            return true;
        }
        return false;
    }

    public function isGroupleader(){
        if(($this->role['is_groupleader'] == 1 || $this->role['is_admin'] == 1) && $this->is_active == 1){
            return true;
        }
        return false;
    }

    
    public function getAction(){
        return Action::where('group_id',$this->group['id'])->where('action_status_id',5)->first();
    }
}
