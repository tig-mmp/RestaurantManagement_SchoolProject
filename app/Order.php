<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
     use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'state', 'item_id','meal_id','responsible_cook_id','start','end',
    ];

    public function meals(){
        return $this->hasOne(Meal::class, 'id', 'meal_id');
    }

    public function items(){
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'responsible_cook_id');
    }




}
