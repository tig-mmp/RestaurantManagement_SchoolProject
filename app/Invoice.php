<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'id', 'state', 'meal_id','nif','name','date','total_price',
    ];

    public function meal(){
        return $this->hasOne(Meal::class, 'id', 'meal_id');
    }

    public function invoiceItem(){
        return $this->belongsToMany(InvoiceItem::class, 'invoice_id','id');
    }
}
