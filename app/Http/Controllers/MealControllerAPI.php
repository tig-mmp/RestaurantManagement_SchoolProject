<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:41
 */

namespace App\Http\Controllers;

use App\Http\Resources\Meal as MealResource;
use App\Invoice;
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
        $invoice = Meal::where('table_number', $request->get('table_number'))->where('state', 'active')->first();
        if ($invoice !== null){
            return response()->json();
        }
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
        if ($request->get('state') === 'paid' || $request->get('state') === 'not paid'){
            $meal->fill(['end' => Carbon::now()->toDateTimeString()]);
        }
        $meal->fill($request->all());
        $meal->fill(['total_price_preview' => round($meal->total_price_preview + $request->get('price'), 2)]);
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
        $query = Order::where('meal_id', $id)
            ->with(array('item' => function($query) use($columns, $column, $dir) {
                $query->orderBy($columns[$column], $dir);
            }))->select('item_id', 'state');
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('type', 'like', '%' . $searchValue . '%');
            });
        }
        $items = $query->paginate($length);
        return ['data' => $items, 'draw' => $request->input('draw')];
    }

    public function onGoingOrders(Request $request, $id)
    {
        return Order::where('meal_id', $id)->whereNotIn('state', ['not delivered', 'delivered'])
            ->with('item:id,price')->select('id', 'state', 'responsible_cook_id', 'item_id')->get();
    }

    public function numberOrders(Request $request, $id){
        return Order::where('meal_id', $id)->count();
    }

    public function invoiceId(Request $request, $id){
        return Invoice::where('meal_id', $id)->where('state', 'pending')->select('id')->first();
    }
}