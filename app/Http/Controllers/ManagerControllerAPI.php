<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\RestaurantTable;
use Illuminate\Support\Facades\Storage;
class ManagerControllerAPI extends Controller
{
    public function index()
    {
        return Item::all();
    }

    public function itemsDataTable(Request $request)
    {
        $columns = ['name', 'type', 'price'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        
        $query = Item::select('id', 'name', 'type', 'price', 'photo_url', 'description')->orderBy($columns[$column], $dir);
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('type', 'like', '%' . $searchValue . '%');
            });
        }
        $items = $query->paginate($length);
        return ['data' => $items, 'draw' => $request->input('draw')];
    }

    public function tablesDataTable(Request $request)
    {
        $columns = ['table_number'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        
        $query = RestaurantTable::select('table_number')->orderBy($columns[$column], $dir);
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('table_number', 'like', '%' . $searchValue . '%');
            });
        }
        $tables = $query->paginate($length);
        return ['data' => $tables, 'draw' => $request->input('draw')];
    }



    public function destroyItem($id)
    {
        $item = Item::findOrFail($id);
        try {
            $item->forceDelete();
        } catch (\Exception $e) {
            $item = Item::findOrFail($id);
            $item->delete();
            return response()->json(null, 204);
        }
        $item->delete();
        
        return response()->json(null, 204);
    }

    public function destroyTable($id)
    {
        $table = RestaurantTable::findOrFail($id);
        try {
            $table->forceDelete();

        } catch (\Exception $e) {
            $table = RestaurantTable::findOrFail($id);
            $table->delete();
            return response()->json(null, 204);
        }
        $table->delete();
        
        return response()->json(null, 204);
    }

    public function uploadFile(Request $request, $id)
    {

        $request->validate([
            'file' => 'image',
        ]);
        $item = Item::findOrFail($id);
        if($item->photo_url != null){
            Storage::delete("public/items/{$item->photo_url}");
        }
        $filename = basename($request->file('file')->store('public/items'));
        $item->fill([
            'photo_url' => $filename,
        ]);
        $item->save();
        return response()->json(null, 204);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'name' => 'required',
                'type' => 'required',

            ]);
        $item = Item::findOrFail($id);
        $item->fill($request->all());
        $item->save();
        return response()->json(null, 204);
    }

    public function store(Request $request){
        $item = new Item();
        $item->fill($request->all());
        $item->fill(['photo_url' => 'default']);
        $item->save();
        return response()->json($item->id, 201);
    }

    public function storeTable(Request $request){
        $table_number = $request->table_number;
        $table = RestaurantTable::create(['table_number' => $table_number]);
        $table->save();
        return response()->json(null, 201);
    }
}
