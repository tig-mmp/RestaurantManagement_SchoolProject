<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItem as OrderItemResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Meal as MealResource;
use App\Meal;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserControllerAPI extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return UserResource::collection(User::paginate(5));
        } else {
            return UserResource::collection(User::all());
        }
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'name' => 'required|min:3',
                'username' => 'required|unique:users,username,'.$id
            ]);
        $user = User::findOrFail($id);
        $old_shift = $user->shift_active;
        $user->fill($request->all());
        if ($old_shift === 1 && $request->get('shift_active') === 0) {
            $user->fill(['last_shift_end' => Carbon::now()->toDateTimeString(),]);
        }
        if ($old_shift === 0 && $request->get('shift_active') === 1) {
            $user->fill(['last_shift_start' => Carbon::now()->toDateTimeString(),]);
        }
        $user->save();
        return new UserResource($user);
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'image',
        ]);
        $user = User::findOrFail($id);
        if($user->profile_photo != null){
            Storage::delete("public/profiles/{$user->profile_photo}");
        }
        $filename = basename($request->file('file')->store('public/profiles'));
        $user->fill([
            'photo_url' => $filename,
        ]);
        $user->save();
        return new UserResource($user);
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        /*if (!Hash::check($request['old_password'], $user->password)){
            new UserResource($user);
        }*/
        $request->validate([
            'password' => 'min:3|confirmed'
        ]);
        $user->fill([
            'password' => Hash::make($user->password)
        ]);
        $user->save();
        return new UserResource($user);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|min:3',
            'username' => 'required|min:3|unique:users,username',
            'email' => 'required|email|unique:users,email'
        ]);
        $user = new User();
        $user->fill($request->all());
        $user->fill(['password' => 'a']);
        $user->save();
        //send mail
        /*
        Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
            $m->from('hello@app.com', 'Your Application');

            $m->to($user->email)->subject('Your Reminder!');
        });*/
        return response()->json(new UserResource($user), 201);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    //waiter
    public function cookOrders(Request $request, $id)
    {
        return OrderItemResource::collection(Order::where('responsible_cook_id', $id)
            ->whereIn('state', ['in preparation', 'confirmed'])
            ->orderByRaw("FIELD(state, 'in prepatation', 'confirmed')")->orderBy('start', 'desc')->get());
    }

    public function meals(Request $request, $id)
    {
        return MealResource::collection(Meal::where('responsible_waiter_id', '=', $id)->where('state', 'active')->get());
    }

    public function waiterOrders(Request $request, $id)
    {
        return OrderItemResource::collection(Order::join('meals', 'orders.meal_id', 'meals.id')
            ->where('responsible_waiter_id', $id)->select('orders.*')->distinct('item_id')
            ->whereIn('orders.state', ['pending', 'confirmed'])->orderBy('orders.start')->get());
    }

    public function invoices(Request $request, $id)
    {
        $query = App\Flight::where('state', 'not paid')->get();
        /*$query = DB::table('meals')
            ->join('items', 'orders.item_id', '=', 'items.id')
            ->select( 'orders.id', 'orders.state',
                'orders.start', 'items.name', 'meals.table_number')
            ->where('users.id', '=', $id)
            ->whereIn('orders.state', ['in preparation', 'confirmed'])
            ->orderByRaw("FIELD(orders.state, 'in prepatation', 'confirmed')")
            ->orderBy('orders.start', 'desc')
            ->paginate(25);*/
        return $query;
    }

    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }
}
