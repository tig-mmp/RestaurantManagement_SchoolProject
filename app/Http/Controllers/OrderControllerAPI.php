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


}