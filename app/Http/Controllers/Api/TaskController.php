<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function all()
    {
        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'Aún no se han registrado tareas',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'tasks' => $tasks,
            'message' => 'Lista de tareas',
            'status' => 200,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'estado' => 'required|in:pendiente,completado,en progreso',
            'prioridad' => 'required|in:bajo,medio,alto',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'No se registró la tarea',
                'errors' => $validator->errors(),
            ], 400);
        };

        Task::create([
            'user_id' => $request->user_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'category_id' => $request->category_id,
            'estado' => $request->estado,
            'prioridad' => $request->prioridad,
        ]);

        return response()->json([
            'message' => 'Tarea registrado con éxito',
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

        $tasks_user = Task::where('user_id', $user->id)->get();

        if ($tasks_user->isEmpty()) {
            return response()->json([
                'message' => 'Este usuario aún no tiene tareas registradas',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Tareas del usuario ' . $user->name . ', encontradas',
            'tasks_user' => $tasks_user,
            'status' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Tarea no encontrada',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'estado' => 'required|in:pendiente,completado,en progreso',
            'prioridad' => 'required|in:bajo,medio,alto',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'No se pudo actualizar la tarea',
                'errors' => $validator->errors(),
            ], 400);
        }

        $task->titulo = $request->titulo;
        $task->descripcion = $request->descripcion;
        $task->category_id = $request->category_id;
        $task->estado = $request->estado;
        $task->prioridad = $request->prioridad;

        $task->save();

        return response()->json([
            'message' => 'Tarea actualizada con exito',
            'status' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Tarea no eliminada',
                'status' => 404,
            ], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Tarea eliminada con éxito',
            'status' => 200,
        ], 200);
    }

    public function showTasksHigh($idUser)
    {
        $tasks = Task::where('user_id', $idUser)
            ->where('prioridad', 'alto')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No hay tareas de este tipo aún',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Mostrando tareas del usuario, con prioridad alta',
            'tasks_high' => $tasks,
            'status' => 200,
        ], 200);
    }

    public function showTasksMedium($idUser)
    {
        $tasks = Task::where('user_id', $idUser)
            ->where('prioridad', 'medio')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No hay tareas de este tipo aún',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Mostrando tareas del usuario, con prioridad media',
            'tasks_high' => $tasks,
            'status' => 200,
        ], 200);
    }

    public function showTasksLow($idUser) 
    {
        $tasks = Task::where('user_id', $idUser)
            ->where('prioridad', 'bajo')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No hay tareas de este tipo aún',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Mostrando tareas del usuario, con prioridad baja',
            'tasks_high' => $tasks,
            'status' => 200,
        ], 200);
    }
}
