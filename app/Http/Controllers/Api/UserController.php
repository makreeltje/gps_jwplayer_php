<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    ///================================================================
    ///ENUM    (Hierarchy -> all rights below the right are added)
    ///================================================================
    // 0 -> no rights
    // 1 -> editing rights
    // 2 -> admin rights

    //Get User By Id
    public function GetUserById(Request $request)
    {
        $roleRequirement = 2; //ADMIN

        $validatedData = $request->validate([
            'userId' => 'required',
        ]);

        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user['id'] == $validatedData['userId'] || $user['role'] >= $roleRequirement) {
                $foundUser = User::where('id', '=', $validatedData['userId'])->first();
                if ($foundUser) {
                    return response($foundUser, 200);
                } else {
                    return response(['message' => 'User not found'], 404);
                }
            } else {
                return response(['message' => 'Insufficient rights'], 403);
            }
        }
        return response(['message' => 'Session Expired'], 405);
    }

    //Get All Users
    public function GetAllUsers()
    {
        $roleRequirement = 2; //ADMIN

        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user['role'] >= $roleRequirement) {
                $foundUsers = User::all();
                if ($foundUsers) {
                    return response($foundUsers, 200);
                } else {
                    return response(['message' => 'Users not found'], 404);
                }
            } else {
                return response(['message' => 'Insufficient rights'], 403);
            }
        }
        return response(['message' => 'Session Expired'], 405);

    }

    //Update User By Id
    public function UpdateUserById(Request $request)
    {
        $roleRequirement = 2; //ADMIN

        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user['id'] == $request['id'] || $user['role'] >= $roleRequirement) {
                $foundUser = User::where('id', '=', $request['id'])->first();
                if ($foundUser) {
                    $foundUser->update([
                        'name' => $request['name'],
                        'role' => $request['role'],
                        'email' => $request['email'],
                    ]);
                    return response($foundUser, 200);
                } else {
                    return response(['message' => 'User not found'], 404);
                }
            } else {
                return response(['message' => 'Insufficient rights'], 403);
            }
        }
        return response(['message' => 'Session Expired'], 405);

    }

    //Delete User By Id
    public function DeleteUserById(Request $request)
    {
        $roleRequirement = 2; //ADMIN

        $validatedData = $request->validate([
            'userId' => 'required',
        ]);

        if (Auth::check()) {
            $user = User::find(Auth::id());
            if ($user['id'] == $validatedData['userId'] || $user['role'] >= $roleRequirement) {
                $foundUser = User::where('id', '=', $validatedData['userId'])->first();
                if ($foundUser) {
                    $foundUser->delete();
                    return response(['message' => 'User deleted'], 200);
                } else {
                    return response(['message' => 'User not found'], 404);
                }
            } else {
                return response(['message' => 'Insufficient rights'], 403);
            }
        }
        return response(['message' => 'Session Expired'], 405);

    }
}
