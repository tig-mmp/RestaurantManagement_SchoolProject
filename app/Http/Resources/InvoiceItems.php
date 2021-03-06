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
            "id" =>$this->id,
            "state" =>$this->state,
            'date' => $this->date,
            'table' => $this->meal->table_number,
            'name' => $this->meal->waiter->name,
            'price_total' =>$this->total_price,
            'items' => InvoiceGetAllItems::collection($this->invoice_items),


        ];
    }
}