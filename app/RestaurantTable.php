<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTable extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'table_number'; // corrige o erro (não encontrava com findOrFail())

    protected $table = 'restaurant_tables';

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'table_number'
    ];

    public function meal(){
        return $this->belongsToMany(Meal::class, 'table_number','table_number');
    }
}
