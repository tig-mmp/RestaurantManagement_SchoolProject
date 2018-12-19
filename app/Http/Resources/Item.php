<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:56
 */

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Item extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'photo_url' => $this->photo_url,
            'price' => $this->price,
        ];
    }
}