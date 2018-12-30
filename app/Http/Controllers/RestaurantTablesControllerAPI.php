<?php
/**
 * Created by PhpStorm.
 * User: tigmm
 * Date: 12-12-2018
 * Time: 14:41
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RestaurantTablesControllerAPI
{

    public function mesasLivres()
    {
        $todasAsMesas = DB::table('restaurant_tables')
            ->select( 'table_number')
            ->get();
        $mesasOcupadas = DB::table('restaurant_tables')
            ->join('meals', 'restaurant_tables.table_number', '=', 'meals.table_number')
            ->select( 'restaurant_tables.table_number')
            ->where('meals.state', '=', 'active')
            ->get();
        $result = $todasAsMesas->pluck('table_number')->diff($mesasOcupadas->pluck('table_number'))
            ->map(function($table_number) {
            return [
                'table_number' => $table_number
            ];
        });
        return $result;
    }
}