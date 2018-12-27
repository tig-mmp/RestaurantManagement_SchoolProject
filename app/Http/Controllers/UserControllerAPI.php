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
use Illuminate\Validation\ValidationException;

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
        if(isset($_FILES['file']) && $_FILES['file']['size'] > 5242880) {
            return response()->json(array(
                'message' => 'The given data was invalid.',
                'errors' => "max size of image is 5MB"
            ),422);
        }
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
        if (!Hash::check($request['old_password'], $user->password)){
            return response()->json(array(
                'message' => 'The given data was invalid.',
                'errors' => array('password' => "old_password and actual_password don't match")
            ),422);
        }
        $request->validate([
            'password' => 'confirmed|min:3|different:old_password',
        ]);
        $user->update(['password' => Hash::make($request['password'])]);
        return response()->json(array(new UserResource($user),$request['password'], Hash::make($request['password'])) , 201);
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
        return OrderItemResource::collection(Order::where('state', 'confirmed')
            ->orWhere('responsible_cook_id', $id)
            ->where('state', 'in preparation')
            ->orderByRaw("FIELD(state, 'in prepatation', 'confirmed')")->orderBy('start', 'desc')->get());
    }

    public function meals(Request $request, $id)
    {
        return MealResource::collection(Meal::where('responsible_waiter_id', '=', $id)->where('state', 'active')->get());
    }

    public function waiterPendingOrders(Request $request, $id)
    {
        return OrderItemResource::collection(Order::join('meals', 'orders.meal_id', 'meals.id')
            ->where('responsible_waiter_id', $id)->select('orders.*')->distinct('item_id')
            ->whereIn('orders.state', ['pending', 'confirmed'])->orderBy('orders.state')
            ->orderBy('orders.start', 'desc')->get());
    }

    public function waiterPreparedOrders(Request $request, $id)
    {
        return OrderItemResource::collection(Order::join('meals', 'orders.meal_id', 'meals.id')
            ->where('responsible_waiter_id', $id)->select('orders.*')->distinct('item_id')
            ->where('orders.state', 'prepared')->orderBy('orders.start')->get());
    }

    public function invoices(Request $request)
    {
       /* $query = Meal::where('state', 'not paid')->get();*/
        $query = DB::table('invoices')
            ->join('meals', 'invoices.meal_id', '=', 'meals.id')
            ->join('users', 'meals.responsible_waiter_id', '=', 'users.id')
            ->select( 'meals.table_number', 'users.name','invoices.total_price')
            ->where('invoices.state', '=', 'not paid')
            ->paginate(25);
        return $query;
    }

    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }
}
