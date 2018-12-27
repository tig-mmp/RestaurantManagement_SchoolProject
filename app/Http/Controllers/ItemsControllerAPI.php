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
        $columns = ['name', 'type', 'price'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $query = Item::select('id', 'name', 'type', 'price', 'photo_url', 'description')->orderBy($columns[$column], $dir);
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('type', 'like', '%' . $searchValue . '%');
            });
        }
        $items = $query->paginate($length);
        return ['data' => $items, 'draw' => $request->input('draw')];
    }
}
