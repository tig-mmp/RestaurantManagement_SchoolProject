<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;

class ItemsControllerAPI extends Controller
{
    public function index()
    {
        return Item::all();
    }
}
