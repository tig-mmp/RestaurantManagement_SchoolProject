<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
	use Notifiable;

    protected $table = 'invoice_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'invoice_id','item_id','quantity','unit_price','sub_total_price',
    ];

    public function invoice(){
        return $this->hasOne(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function item(){
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}
