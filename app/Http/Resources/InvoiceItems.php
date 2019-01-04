<?php


namespace App\Http\Resources;

use App\Item;
use App\Http\Resources\Item as ItemResource;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Builder;

class InvoiceItems extends Resource
{
    public function toArray($request)
    {
        return [
            'date' => $this->date,
            'table' => $this->meal->table_number,
            'name' => $this->meal->waiter,
            'price_total' =>$this->total_price,
            //'items' => $this->invoice_items->id,
            //'item_name' => $this->invoice_items->quantity,

        ];
    }
}