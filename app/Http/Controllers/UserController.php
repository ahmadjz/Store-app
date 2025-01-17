<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\HttpResponses;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StorUserRequest;

class UserController extends Controller
{
    use HttpResponses;
    function Login(LoginUserRequest $request){
       
        $request->validated($request->only(['email', 'password']));

        if(!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'customer' => [
                'id' => $user->id,
                'name' =>$user->name,
                'numOfNotofocztion' =>$user->numofnotification,
            ],
            'contacts' => [
                'phone' => $user->mobile_phone,
                'email' => $user->email,
                'link' => $user->link
            ],
            'token' => $user->createToken('API Token')->plainTextToken
        ],'User Logged In Successfully');

    }

    public function register(StorUserRequest $request)
    {
        $request->validated($request->only(['name', 'email', 'password','mobile_number']));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_phone' => $request->mobile_phone
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'You have succesfully been logged out and your token has been removed'
        ]);
    }
       

}
