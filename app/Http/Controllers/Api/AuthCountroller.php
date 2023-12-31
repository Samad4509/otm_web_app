<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthCountroller extends Controller
{
    public function register(Request $request)
    {
        $validator = validator::make($request->all(),[
            'name'=>'required|min:2|max:100',
            'email'=>'required|email|unique:users',
            'phone' =>'required', 'string', 'max:255', 'unique:users', 'regex:/^[0-9\+\-\s]+$/',
            'password'=>'required|min:6|max:100',
            'confirm_password'=>'required|same:password'
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
        ]);

        return response()->json([
            'message'=>'Registration Successfull',
            'data'=>$user,
        ],200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth-token')->plainTextToken;
        
            return response()->json([
                'message' => 'Login Successful',
                'token' => $token,
                'data' => $user
            ], 200);
        }  
        else {
            return response()->json([
                'message' => 'Incorrect Password',
            ], 400);
        }
        
    }
}
