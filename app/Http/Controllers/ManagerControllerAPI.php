<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\RestaurantTable;
use App\User;
use App\Meal;
use App\Invoice;
use App\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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

    public function userDataTable(Request $request)
    {
        $columns = ['name', 'username', 'email', 'type','blocked'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        
        $query = User::withTrashed()->select('id', 'name', 'username', 'email', 'type', 'blocked',  'photo_url', 'last_shift_start', 'last_shift_end', 'email_verified_at', 'shift_active', 'deleted_at')->orderBy($columns[$column], $dir);
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('type', 'like', '%' . $searchValue . '%')
                ->orWhere('username', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%');
            });
        }
        $users = $query->paginate($length);
        return ['data' => $users, 'draw' => $request->input('draw')];
    }

    public function invoiceDataTable(Request $request)
    {
        $columns = ['id','table_number','meal_id','name','state','total_price','created_at'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $filterByDate = $request->input('date');
        $hasFilterStates = $request->input('filterState.0');
        $filterStates = [];
        if($hasFilterStates){
            foreach ($request->input('filterState') as $value){
                array_push($filterStates, $value);
            }
        }

        
        $query = DB::table('invoices')
            ->join('meals', 'invoices.meal_id', '=', 'meals.id')
            ->join('users', 'meals.responsible_waiter_id', '=', 'users.id')
            ->select(
                'invoices.id',
                'meals.table_number',
                'meal_id', 
                'users.name',
                'invoices.state',
                'invoices.total_price',
                'invoices.created_at'
            )->orderBy($columns[$column], $dir);

        if($hasFilterStates){
            $query->where(function($query) use ($filterStates) {
                foreach ($filterStates as $state){
                    $query->orWhere('invoices.state', '=', $state);
                }
            });
        }

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('users.name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($filterByDate) {
            $query->where(function($query) use ($filterByDate) {
                $query->where('meals.created_at', 'like', date($filterByDate). '%');
            });
        }

        $invoices = $query->paginate($length);
        return ['data' => $invoices, 'draw' => $request->input('draw')];
    }

    public function mealDataTable(Request $request)
    {
        $columns = ['id','table_number', 'name','state','total_price_preview','created_at'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $filterByDate = $request->input('date');
        $hasFilterStates = $request->input('filterState.0');
        $filterStates = [];
        if($hasFilterStates){
            foreach ($request->input('filterState') as $value){
                array_push($filterStates, $value);
            }
        }
        $query = DB::table('meals')
            ->join('users', 'meals.responsible_waiter_id', '=', 'users.id')
            ->select(
                'meals.id',
                'meals.state',
                'meals.table_number', 
                'users.name',
                'meals.total_price_preview',
                'meals.created_at')->orderBy($columns[$column], $dir);
        
        if($hasFilterStates){
            $query->where(function($query) use ($filterStates) {
                foreach ($filterStates as $state){
                    $query->orWhere('meals.state', '=', $state);
                }
            });
        }

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('users.name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($filterByDate) {
            $query->where(function($query) use ($filterByDate) {
                $query->where('meals.created_at', 'like', date($filterByDate). '%');
            });
        }
        //return $this->getSql($query); 
        $meals = $query->paginate($length);
        return ['data' => $meals, 'draw' => $request->input('draw')];
    }

    public function orderDataTable(Request $request)
    {
        $columns = ['state','name','type','price','meal_id'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $meal_id = $request->input('meal_id');
        
        $query = DB::table('orders')
            ->join('items', 'orders.item_id', '=', 'items.id')
            ->select('orders.meal_id','orders.state','items.name','items.type','items.price')
            ->where('orders.meal_id', '=', $meal_id)->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('items.name', 'like', '%' . $searchValue . '%');
            });
        }

        $orders = $query->paginate($length);
        return ['data' => $orders, 'draw' => $request->input('draw')];
    }

    public function invoiceItemsDataTable(Request $request)
    {
        $columns = ['type','name','quantity','unit_price','sub_total_price','invoice_id'];
        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $invoice_id = $request->input('invoice_id');
        
        $query = DB::table('invoice_items')
            ->join('items', 'invoice_items.item_id', '=', 'items.id')
            ->select(
                'items.type',
                'items.name',
                'invoice_items.quantity',
                'invoice_items.unit_price',
                'invoice_items.sub_total_price',
                'invoice_items.invoice_id'
            )
            ->where('invoice_items.invoice_id', '=', $invoice_id)->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('items.name', 'like', '%' . $searchValue . '%');
            });
        }

        $orders = $query->paginate($length);
        return ['data' => $orders, 'draw' => $request->input('draw')];
    }

     public function getSql($b){
         $query = str_replace(array('?'), array('\'%s\''), $b->toSql());
         $query = vsprintf($query, $b->getBindings());
         dump($query);

         return sizeof($b->get());
         // return null;
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

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        try {
            $user->forceDelete();

        } catch (\Exception $e) {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(null, 204);
        }
        $user->delete();
        
        return response()->json(null, 204);
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'image',
        ]);
        $request->validate(['file' => 'image',]);
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
            'name' => 'required|min:3',
            'type' => 'required|in:dish,drink',
            'price' => 'required|numeric|min:0.01|max:999',
            'description' => 'required'
        ]);
        $item = Item::findOrFail($id);
        $item->fill($request->all());
        $item->save();
        return response()->json(null, 204);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'type' => 'required|in:manager,cook,waiter,cashier',
        ]);
        $user = User::findOrFail($id);
        if ($user->username != $request->username) {
            $request->validate([
                'username' => 'required|unique:users,username',
            ]);
        }
        if ($user->email != $request->email) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
            ]);
        }
        $user->fill($request->all());
        $user->save();
        return response()->json(null, 204);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|min:3',
            'type' => 'required|in:dish,drink',
            'price' => 'required|numeric|min:0.01|max:999',
            'description' => 'required',
            'file' => 'required|image',
        ]);
        $filename = basename($request->file('file')->store('public/items'));
        $item = new Item();
        $item->fill($request->all());
        $item->fill(['photo_url' => $filename]);
        $item->save();
        
        return response()->json($item->id, 201);
    }

    public function storeTable(Request $request){
        $request->validate([
            'table_number' => 'required|unique:restaurant_tables,table_number|digits_between:0,99',
        ]);
        $table_number = $request->table_number;
        $table = RestaurantTable::create(['table_number' => $table_number]);
        $table->save();
        return response()->json(null, 201);
    }

    public function pendingInvoicesAsNotPaid($id)
    {
        $meal = Meal::findOrFail($id);
        if ($meal->state == 'terminated') {
            $meal->fill(['state' => 'not paid']);
        }

        $invoice = Invoice::where('meal_id', '=', $meal->id)->update(['state' => 'not paid']);
        

        $orders = Order::where('meal_id', '=', $meal->id)
        ->where('state', '!=', 'delivered')
        ->update(['state' => 'not delivered']);

        $meal->save();

        return $meal;
    }

    public function paidInvoicesItemsToPDF(Request $request)
    {
        $invoice_id = $request->input('id');
        $query = DB::table('invoice_items')
            ->join('items', 'invoice_items.item_id', '=', 'items.id')
            ->select(
                'items.type',
                'items.name',
                'invoice_items.quantity',
                'invoice_items.unit_price',
                'invoice_items.sub_total_price',
                'invoice_items.invoice_id'
            )
            ->where('invoice_items.invoice_id', '=', $invoice_id);

        $items = $query->get();
        return ['data' => $items];
    }

    public function paidInvoicesToPDF(Request $request)
    {
        $invoice_id = $request->input('id');
        $query = DB::table('invoices')
            ->join('meals', 'invoices.meal_id', '=', 'meals.id')
            ->join('users', 'meals.responsible_waiter_id', '=', 'users.id')
            ->select(
                'invoices.id',
                'meals.table_number',
                'meal_id', 
                'users.name',
                'invoices.state',
                'invoices.total_price',
                'invoices.created_at'
            )->where('invoices.id', '=', $invoice_id);

        $invoice = $query->get();
        return ['data' => $invoice];
    }

    public function statisticAvgOrdersByDayCook($id)
    {
        /*$query = DB::table('orders')
            ->selectRaw('COUNT(orders.id) as orders, DATE(orders.created_at) day')
            ->where('orders.responsible_cook_id', '=', $id)
            ->groupBy('day')->get();*/

        $query = DB::table('orders')
            ->where('orders.responsible_cook_id', '=', $id)
            ->selectRaw('COUNT(orders.created_at) as orders, DATE(orders.created_at) day, orders.responsible_cook_id as waiter')
            ->groupBy('waiter','day')
            ->get();

        $items = $query;
        return ['data' => $items];
    }
}
