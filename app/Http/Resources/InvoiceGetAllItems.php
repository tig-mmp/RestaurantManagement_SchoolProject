<?php


namespace App\Http\Resources;

use App\Item;
use App\Http\Resources\Item as ItemResource;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Builder;

class InvoiceGetAllItems extends Resource
{
    public function toArray($request)
    {
        return [

             "quantity" => $this->quantity,
             "unit_price" => $this->unit_price,
             "sub_total_price" => $this->sub_total_price,
             "name" => $this->item->name,

           // 'items' => $this->item,

        ];
    }
}