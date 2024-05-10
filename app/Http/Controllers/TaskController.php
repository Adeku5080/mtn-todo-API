<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * create a task
     */
    public function createTask(Request $request, Todo $todo)
    {
        $user = Auth::user();

        if ($user->id !== $todo->user_id) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);

        }

        $request->validate([
            'description' => 'required',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $task = Task::create([
            'description' => $request->description,
            'due_date' => Carbon::parse($request->due_date)->endOfDay(),
            'todo_id' => $todo->id,

        ]);

        return new JsonResponse(['message' => 'task created succesfully',
            'task' => $task,
        ], 201);
    }

    /**
     * get all tasks for a todo
     */
    public function getAllTaskForATodo(Todo $todo, Request $request)
    {
        if (Auth::user()->id !== $todo->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tasksQuery = $todo->tasks();

        if ($request->has('status')) {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $tasksQuery->orderBy($request->sort_by, $request->sort_order);
        }
        $tasks = $tasksQuery->paginate($request->per_page ?? 10);

        return response()->json(['data' => $tasks], 200);

    }

    /**
     * get a task
     */
    public function getTask(Task $task)
    {
        if ($task->todo->user_id !== Auth::user()->id) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        return new JsonResponse(['data' => $task], 200);

    }

    /**
     * update a task
     */
    public function updateTask(Task $task, Request $request)
    {
        if ($task->todo->user_id !== Auth::user()->id) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);

        }
        $request->validate([
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'nullable|in:completed,pending',
        ]);

        $task->update([
            'description' => $request->description ?? $task->description,
            'status' => $request->status ?? $task->status,
            'due_date' => $request->due_date ? Carbon::parse($request->due_date)->endOfDay() : $task->due_date,
        ]);

        return new JsonResponse(['data' => $task->refresh()], 200);

    }

    /**
     * delete a task
     */
    public function deleteTask(Task $task)
    {
        if ($task->todo->user_id !== Auth::user()->id) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);

        }

        if (! $task) {
            return new JsonResponse(['message' => 'record not found'], 404);
        }
        $task->delete();

        return new JsonResponse(null, 204);
    }
}
