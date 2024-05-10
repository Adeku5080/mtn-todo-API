<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * create a todo
     */
    public function createTodo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|max:255|min:1|string',

        ]);

        $todo = Todo::create([
            'name' => $request->name,
            'user_id' => $user->id,
        ]);

        return new JsonResponse(['message' => 'todo created succesfully', 'data' => $todo], 201);

    }

    /**
     * get all todos for a user
     */
    public function getAllTodosForUser(Request $request)
    {
        $user = Auth::user();

        $query = Todo::where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->sort_by, $request->sort_order);
        }

        $perPage = $request->input('per_page', 10);
        $todos = $query->paginate($perPage);

        return new JsonResponse(['data' => $todos], 200);

    }

    /**
     * get a todo
     */
    public function getTodo(Todo $todo)
    {
        $user = Auth::user();
        if ($user->id !== $todo->user_id) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        return new JsonResponse(['data' => $todo], 200);
    }

    /**
     * update a todo
     */
    public function updateTodo(Todo $todo, Request $request)
    {
        $user = Auth::user();
        if ($user->id !== $todo->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required',
        ]);

        $todo->update([
            'name' => $request->name,
        ]);

        return response()->json(['data' => $todo, 'message' => 'Todo has been updated'], 200);
    }

    /**
     * delete a todo
     */
    public function deleteTodo(Todo $todo)
    {
        $user = Auth::user();
        if ($user->id !== $todo->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todo->delete();

        return response()->json(null, 204);
    }
}
