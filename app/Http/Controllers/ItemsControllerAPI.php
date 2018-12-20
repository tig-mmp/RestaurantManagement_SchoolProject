<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Item as ItemResource;
use App\Item;
use Illuminate\Support\Facades\DB;

class ItemsControllerAPI extends Controller
{
    public function index()
    {
        return Item::all();
    }

    public function dishes()
    {
        return ItemResource::collection(Item::where('type', 'dish')->get());
    }

    public function drinks()
    {
        return ItemResource::collection(Item::where('type', 'drink')->get());
    }
}
