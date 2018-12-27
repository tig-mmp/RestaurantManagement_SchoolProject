<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id', 'name', 'username', 'email', 'password', 'type', 'blocked', 'photo_url', 'shift_active', 'last_shift_start', 'last_shift_end'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->where('blocked', 0)->first();
    }

    public function meal(){
        return $this->belongsToMany(Meal::class, 'responsible_waiter_id','id');
    }

    public function order(){
        return $this->belongsToMany(Order::class, 'responsible_cook_id','id');
    }
}
