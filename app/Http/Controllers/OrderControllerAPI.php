<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:41
 */

namespace App\Http\Controllers;

use App\Http\Resources\Order as OrderResource;
use App\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderControllerAPI
{

    public function update(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|min:3',
        ]);
        $order = Order::findOrFail($id);
        $order->fill([
            'state' => $request->state,
        ]);
        $order->save();
        return new OrderResource($order);
    }

    public function store(Request $request){
        $order = new Order();
        $order->fill($request->all());
        $order->fill([
            'state' => 'pending',
            'start' => Carbon::now()
        ]);
        $order->save();
        return response()->json(new OrderResource($order), 201);
    }

}