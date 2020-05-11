<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);
        $validatedData['password'] = bcrypt($validatedData['password']);

        $reponse = ($this->dispatch(new register($validatedData)));
        return $response;
    }

    public function Login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $response = ($this->dispatch(new login($loginData)));
        return $response;
    }

    public function Logout(Request $request){
        $user = auth()->guard('api')->user();

        foreach ($user->tokens as $token) {
            $token->revoke();
        }
        Auth::Logout($request);
    }

    public function testAuth(Request $request)
    {
        if (Auth::Check($request)) 
            return 'This Authentication is Valid';
        else
            return 'This Authentication is Invalid';
    }
}
