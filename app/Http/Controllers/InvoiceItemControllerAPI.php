<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceItem as InvoiceItemResource;
use App\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InvoiceItemControllerAPI extends Controller
{
    public function update(Request $request, $invoice, $item)
    {
        $invoice = InvoiceItem::where('invoice_id', $invoice)->where('item_id', $item)->first();
        $invoice->update([
            'quantity' => $invoice->quantity + 1,
            'sub_total_price' => round($invoice->sub_total_price + $invoice->unit_price, 2)
        ]);
        return response()->json(new InvoiceItemResource($invoice), 202);
    }

    public function store(Request $request)
    {
        $invoice = InvoiceItem::where('invoice_id', $request->get('invoice_id'))
            ->where('item_id', $request->get('item_id'))->first();
        if ($invoice !== null){
            return response()->json();
        }
        $invoice = new InvoiceItem();
        $invoice->fill([
            'invoice_id' => $request->get('invoice_id'),
            'item_id' => $request->get('item_id'),
            'quantity' => 1,
            'unit_price' => $request->get('price'),
            'sub_total_price' => $request->get('price')
        ]);
        $invoice->save();
        return response()->json(new InvoiceItemResource($invoice), 201);
    }

    public function find(Request $request){
        return InvoiceItem::where('invoice_id', $request->get('invoice_id'))
            ->where('item_id', $request->get('item_id'))->first();
    }

}
