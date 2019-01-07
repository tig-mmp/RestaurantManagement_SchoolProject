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

    public function paginate(Request $request)
    {
        $length = $request->input('length');
        $query = Item::select('id', 'name', 'type', 'price', 'photo_url', 'description');
        $items = $query->paginate($length);
        return ['data' => $items, 'draw' => $request->input('draw')];
    }
}
