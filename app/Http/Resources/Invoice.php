<?php


namespace App\Http\Resources;

use App\Item;
use App\Http\Resources\Item as ItemResource;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'meal_id' => $this->meal_id,
            'nif' => $this->nif,
            'name' => $this->name,
            'table_number' => $this->meal->table->table_number,
            'date' => $this->date,
            'total_price' => $this->total_price,
            'waiter' => $this->meal->waiter->name
        ];
    }
}
