<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'blocked' => $this->blocked,
            'photo_url' => $this->photo_url,
            'shift_active' => $this->shift_active,
            'last_shift_start' => $this->last_shift_start,
            'last_shift_end' => $this->last_shift_end
        ];
    }
}
