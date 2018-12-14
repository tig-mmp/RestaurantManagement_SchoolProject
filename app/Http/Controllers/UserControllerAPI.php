<?php

namespace App\Http\Controllers;

use App\Meal;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\User;
use App;
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
        $user->fill($request->all());
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

    public function orders(Request $request, $id)
    {
        $query = DB::table('orders')
            ->join('users', 'orders.responsible_cook_id', '=', 'users.id')
            ->join('meals', 'orders.meal_id', '=', 'meals.id')
            ->join('items', 'orders.item_id', '=', 'items.id')
            ->select( 'orders.id', 'orders.state',
                'orders.start', 'items.name', 'meals.table_number')
            ->where('users.id', '=', $id)
            ->whereIn('orders.state', ['in preparation', 'confirmed'])
            ->orderByRaw("FIELD(orders.state, 'in prepatation', 'confirmed')")
            ->orderBy('orders.start', 'desc')
            ->paginate(25);
        return $query;
    }

    public function invoices(Request $request)
    {
        $query = Meal::where('state', 'not paid')->get();
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


    public function meals(Request $request, $id)
    {
        $query = DB::table('meals')
            ->join('users', 'responsible_waiter_id', '=', 'users.id')
            ->where('responsible_waiter_id', '=', $id)
            ->where('meals.state', '=', 'active')
            ->paginate(25);
        return $query;
    }

    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }
}
