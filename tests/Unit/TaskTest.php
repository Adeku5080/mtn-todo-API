<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_createTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/todos/$todo->id/tasks", [
            'description' => 'Test Task',
            'due_date' => '2024-05-10',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'task' => [
                    'id',
                    'description',
                    'statusColor',
                    'due_date',
                    'todo_id',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_getAllTaskForATodo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(3)->create(['todo_id' => $todo->id]);

        $response = $this->getJson("/api/todos/$todo->id/tasks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'description',
                            'due_date',
                            'todo_id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_getTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['todo_id' => $todo->id]);

        $response = $this->getJson("/api/tasks/$task->id");

        $response->assertStatus(200)
            ->assertJson([
                'data' => $task->toArray(),
            ]);
    }

    public function test_updateTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['todo_id' => $todo->id]);

        $response = $this->patchJson("/api/tasks/$task->id", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'status' => 'completed',
                ],
            ]);
    }

    public function test_deleteTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['todo_id' => $todo->id]);

        $response = $this->deleteJson("/api/tasks/$task->id");

        $response->assertStatus(204);
        $this->assertSoftDeleted($task);
    }
}
