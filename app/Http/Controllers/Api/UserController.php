<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function all()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No hay usuarios registrados',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'users' => $users,
            'message' => 'Lista de usuarios',
            'status' => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'No se registró al usuario',
                'errors' => $validator->errors(),
            ], 400);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'status' => 201,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'La actualización no fue exitosa',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Datos del usuario actualizado correctamente',
            'status' => 200,
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'El usuario no existe',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario encontrado',
            'user' => $user,
            'status' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
                'status' => 404,
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado',
            'status' => 200,
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
                'status' => 400,
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'status' => 401,
            ], 401);
        }

        /* Auth::login($user); */

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
            'status' => 200,
        ], 200);
    }

    public function logout()
    {
        /* Auth::logout(); */

        return response()->json([
            'message' => 'Usuario desconectado',
            'status' => 200,
        ], 200);
    }
}
