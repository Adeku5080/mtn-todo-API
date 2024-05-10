<?php

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\User;
// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_createTodo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/todos', ['name' => 'Test Todo']);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_getAllTodosForUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Todo::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/todos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'user_id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_getTodo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/todos/$todo->id");

        $response->assertStatus(200)
            ->assertJson([
                'data' => $todo->toArray(),
            ]);
    }

    public function test_updateTodo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->patchJson("/api/todos/$todo->id", ['name' => 'Updated Todo']);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'name' => 'Updated Todo',
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'name' => 'Updated Todo',
        ]);
    }

    public function test_deleteTodo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $todo = Todo::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/todos/$todo->id");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }
}
