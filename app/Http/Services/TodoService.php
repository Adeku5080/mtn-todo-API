<?php

namespace App\Http\Services;

use App\Models\Todo;

class TodoService
{
    public function create(array $data, $user)
    {
        $todo = Todo::create([
            'name' => $data['name'],
            'user_id' => $user->id,
        ]);

        return $todo;

    }
}
