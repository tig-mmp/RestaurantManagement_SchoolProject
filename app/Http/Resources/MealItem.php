<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:56
 */

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;

class MealItem extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'state' => $this->state,
            'table_number' => $this->table->table_number,
            'start' => $this->start,
            'end' => $this->end,
            'responsible_waiter_id' => $this->responsible_waiter_id,
            'total_price_preview' => $this->total_price_preview,
        ];
    }
}