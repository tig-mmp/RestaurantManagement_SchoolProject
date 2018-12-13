<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return DB::table('items')->where('type', 'dish')->paginate(25);
    }

    public function drinks()
    {
        return DB::table('items')->where('type', 'drink')->paginate(25);
    }
}
