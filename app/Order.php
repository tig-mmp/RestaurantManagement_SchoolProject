<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
     use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'orders';

    protected $fillable = [
        'id', 'state', 'item_id','meal_id','responsible_cook_id','start','end',
    ];

    public function meal(){
        return $this->hasOne(Meal::class, 'id', 'meal_id');
    }

    public function item(){
        return $this->hasOne(Item::class, 'id', 'item_id')
            ->select('id', 'name', 'type','description','photo_url','price')
            ->withTrashed();

    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'responsible_cook_id');
    }




}
