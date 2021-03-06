<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Meal extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'state', 'table_number','start','end','responsible_waiter_id','total_price_preview',
    ];

    public function table(){
        return $this->hasOne(RestaurantTable::class, 'table_number', 'table_number')->withTrashed();
    }

    public function waiter(){
        return $this->hasOne(User::class, 'id', 'responsible_waiter_id')->withTrashed();
    }

    public function order(){
        return $this->belongsToMany(Order::class, 'meal_id','id');
    }

    public function invoice(){
        return $this->hasMany(Invoice::class, 'meal_id','id');
    }

}
