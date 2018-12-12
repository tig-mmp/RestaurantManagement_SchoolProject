<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RestaurantTable extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'table_number'
    ];

    public function meal(){
        return $this->belongsToMany(Meal::class, 'table_number','table_number');
    }
}
