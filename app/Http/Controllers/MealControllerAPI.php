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

}