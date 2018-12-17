<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:56
 */

namespace App\Http\Resources;

use App\Item;
use \App\Order;
use App\Http\Resources\Item as ItemResource;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Builder;

class OrderItem extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'start' => $this->start,
            'photo_url' => $this->item->photo_url,
            'name' => $this->item->name,
            'state' => $this->state,
            'meal_id' => $this->meal_id,
            'table_number' => $this->meal->table->table_number,
        ];
    }
}