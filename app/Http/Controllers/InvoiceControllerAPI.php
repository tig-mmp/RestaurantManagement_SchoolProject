<?php

namespace App\Http\Controllers;

use App\InvoiceItem;
use App\Invoice;
use App\Http\Resources\Invoice as InoiceResource;
use App\Http\Resources\InvoiceItems as InvoiceItems;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceControllerAPI extends Controller
{
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());
        return new InoiceResource($invoice);
    }

    public function store(Request $request)
    {
        $invoice = Invoice::where('meal_id', $request->get('meal_id'))->where('state', 'pending')->first();
        if ($invoice !== null){
            return response()->json();
        }
        $invoice = new Invoice();
        $invoice->fill([
            'meal_id' => $request->get('meal_id'),
            'state' => 'pending',
            'date' => Carbon::now()->toDateString(),
            'total_price' => 0
        ]);
        $invoice->save();
        return response()->json(new InoiceResource($invoice), 201);
    }

    public function totalPrice(Request $request, $id)
    {
        return Invoice::findOrFail($id)->invoice_items()->sum('sub_total_price');
    }


}
