<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:41
 */

namespace App\Http\Controllers;

use App\Http\Resources\Meal as MealResource;
use App\Meal;
use App\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MealControllerAPI
{

    public function store(Request $request){
        $request->validate([
            'table_number' => 'numeric',
        ]);
        $meal = new Meal();
        $meal->fill([
            'state' => 'active',
            'table_number' => $request->get('table_number'),
            'start' => Carbon::now()->toDateTimeString(),
            'responsible_waiter_id' => $request->get('id'),
            'total_price_preview' => 0
        ]);
        $meal->save();
        return response()->json(new MealResource($meal), 201);
    }

    public function update(Request $request, $id)
    {
        $meal = Meal::findOrFail($id);
        $meal->fill(['total_price_preview' => $meal->total_price_preview + $request->get('price')]);
        $meal->save();
        return new MealResource($meal);
    }

    public function summary(Request $request, $id)
    {
        return Meal::where('id', $id)->with('table:table_number')
            ->select('meals.total_price_preview', 'meals.table_number')->first();
    }

    public function items(Request $request, $id)
    {
        $columns = ['name', 'type', 'price'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $query = Order::where('meal_id', $id)->join('items', 'orders.item_id', 'items.id')
            ->select('items.name', 'items.type', 'items.price', 'photo_url')
            ->orderBy($columns[$column], $dir);
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