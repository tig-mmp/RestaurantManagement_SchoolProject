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
            'date' => $this->invoice->date,
            'table' => $this->invoice->meal->table_number,
            'name' => $this->invoice->meal->waiter->name,
            'price_total' =>$this->invoice->total_price,
            'invoices_items' => $this->item,
           // 'items' => $this->item,

        ];
    }
}