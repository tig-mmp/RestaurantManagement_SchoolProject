<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
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
}
