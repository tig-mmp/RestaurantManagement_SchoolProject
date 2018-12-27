<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Item extends Model
{
	use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id', 'name', 'type','description','photo_url','price',
    ];

    public function invoiceItem(){
        return $this->belongsTo(InvoiceItem::class, 'item_id','id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'item_id','id');
    }
}
