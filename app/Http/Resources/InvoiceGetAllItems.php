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
             "id" => $this->invoice_id,
             "item" => array($this->item_id),
           // 'items' => $this->item,

        ];
    }
}