<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function all(){
        $users = User::all();

        return response()->json([
            'users' => $users,
            'message' => 'Lista de usuarios',
            'status' => 200,
        ], 200);
    }
}
