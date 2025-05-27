<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use Validator;
use Carbon\Carbon;
use DB;

class UserController extends Controller
{
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'first_name'=> 'required|max:50',
                'last_name'=> 'required|max:50',
                'email' => 'required|string|email|max:255|unique:users',
                'phone_no'=>'required|min:10',
                'dob' =>'required|date',
                'address1'=> 'required|max:255',
                'address2'=>'max:255',
                'pincode'=>'required|min:4|max:6',
                'city'=>'required',
                'state'=>'required',
                'type'=>'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
    
            $user = User::create([
 
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'dob' => $request->dob,
            ]);

        
            if($user->save()){
                $address = Address::create([
                    'user_id' => $user['id'],
                    'address1' => $request->address1,
                    'address2' => $request->address2,
                    'pincode' => $request->pincode,
                    'city' => $request->city,
                    'state' => $request->state,
                    'type' => $request->type, 
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'User stored successfully.',
                'user' => $user,
                'address' => $address
            ]);

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function update(Request $request){
        try {
            $user = User::find($request->id);

            if(!empty($user)){

                $validator = Validator::make($request->all(), [
                    'first_name'=> 'max:50',
                    'last_name'=> 'max:50',
                    'email' => 'string|email|max:255|unique:users',
                    'phone_no'=>'min:10',
                    'dob' =>'date',
                    'address1'=> 'max:255',
                    'address2'=>'max:255',
                    'pincode'=>'min:4|max:6',
                ]);
        
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 401);
                }
    
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->phone_no = $request->phone_no;
                $user->dob = $request->dob;

                $user->save();

                $address = Address::where('user_id',$request->id)->first();

                if(!empty($address)){
                    $address->user_id = $request->id;
                    $address->address1 = $request->address1;
                    $address->address2 = $request->address2;
                    $address->pincode = $request->pincode;
                    $address->city = $request->city;
                    $address->state = $request->state;
                    $address->type = $request->type;

                    $address->save();
                }

            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user,
                'address' => $address
            ]);
 

        } catch (Exception $e) {
            dd($e->getMessage());
        }
        
    }

    public function searchUsers(Request $request){
       try {
        
        // Search By String : first_name,last_name,email
        // $result = User::where(DB::raw('BINARY `first_name`'), 'LIKE',"%{$request->string}%")
        //         ->orWhere(DB::raw('BINARY `last_name`'), 'LIKE',"%{$request->string}%")
        //         ->orWhere(DB::raw('BINARY `email`'), 'LIKE',"%{$request->string}%")
        //         ->get();
        //         return ['result' => $result];
        

        // Search by city 
        //   $result = User::with('addresses')
        //   ->whereHas('addresses',function($query) use ($request) {
        //     $query->where(DB::raw('BINARY `address1`'), 'LIKE',"%{$request->city}%")
        //     ->Orwhere(DB::raw('BINARY `address2`'), 'LIKE',"%{$request->city}%");
        //         })
        //   ->get();
        //     return ['result' => $result];

        $result = User::with('addresses')

            ->where(DB::raw('BINARY `first_name`'), 'LIKE',"%{$request->string}%")
                    ->orWhere(DB::raw('BINARY `last_name`'), 'LIKE',"%{$request->string}%")
                    ->orWhere(DB::raw('BINARY `email`'), 'LIKE',"%{$request->string}%")

            ->whereHas('addresses',function($query) use ($request) {
            $query->where(DB::raw('BINARY `address1`'), 'LIKE',"%{$request->city}%")
            ->Orwhere(DB::raw('BINARY `address2`'), 'LIKE',"%{$request->city}%");
                })

            ->get();
              
          return ['result' => $result];


                
       } catch (Exception $e) {
            dd($e->getMessage());
       }
    }
}
