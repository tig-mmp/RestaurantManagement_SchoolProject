<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:41
 */

namespace App\Http\Controllers;

use App\Http\Resources\OrderItem as OrderItemResource;
use App\Http\Resources\Order as OrderResource;
use App\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderControllerAPI
{

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->fill($request->all());
        if ($order->state === 'delivered' || $order->state === 'not delivered'){
            $order->fill(['end' => Carbon::now()->toDateTimeString()]);
        }
        $order->save();
        return new OrderItemResource($order);
    }

    public function store(Request $request){
        $order = new Order();
        $order->fill($request->all());
        $order->fill([
            'responsible_cook_id' => null,
            'state' => 'pending',
            'start' => Carbon::now()
        ]);
        $order->save();
        return response()->json(new OrderItemResource($order), 201);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }

}