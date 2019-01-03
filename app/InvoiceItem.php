<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
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
    public $timestamps = false;
    protected $primaryKey = ['invoice_id', 'item_id'];
    public $incrementing = false;

    protected $fillable = [
        'invoice_id','item_id','quantity','unit_price','sub_total_price',
    ];

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id','id');
       // return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function item(){
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    protected function setKeysForSaveQuery(Builder $query)//isto é para fazer update, porque tem chave composta
    {
        return $query->where('invoice_id', $this->getAttribute('invoice_id'))
            ->where('item_id', $this->getAttribute('item_id'));
    }

}
