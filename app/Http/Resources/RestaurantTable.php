<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 17:15
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RestaurantTable extends Resource
{
    public function toArray($request)
    {
        return [
            'table_number' => $this->table_number,
        ];
    }
}