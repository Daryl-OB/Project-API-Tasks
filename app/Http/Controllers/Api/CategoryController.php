<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function all()
    {
        $categories = Category::all();

        if($categories->isEmpty()){
            return response()->json([
                'message' => 'Aún no se han registrado categorias',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'categories' => $categories,
            'message' => 'Lista de categorias',
            'status' => 200,
        ], 200); 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'No se registró la categoria',
                'errors' => $validator->errors(),
            ]);
        }

        Category::create([
            'user_id' => $request->user_id,
            'nombre' => $request->nombre,
        ]);

        return response()->json([
            'message' => 'Categoria registrada con éxito',
            'status' => 201,
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Este usuario no existe',
                'status' => 404,
            ]);
        }

        $categories_user = Category::where('user_id', $user->id)->get();

        if ($categories_user->isEmpty()) {
            return response()->json([
                'message' => 'Este usuario aún no tiene categorias registradas',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Categorias del usuario ' . $user->name . ', encontradas',
            'categories_user' => $categories_user,
            'status' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria no encontrada',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'No se pudo actualizar la categoria',
                'errors' => $validator->errors(),
            ], 400);
        }

        $category->nombre = $request->nombre;
        
        $category->save();

        return response()->json([
            'message' => 'Categoria actualizada con exito',
            'status' => 200,
        ], 200); 
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Categoria no eliminada',
                'status' => 404,
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoria eliminada con éxito',
            'status' => 200,
        ], 200);
    }
}
