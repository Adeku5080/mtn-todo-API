<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
// use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'authorisation' => [
                        'token',
                        'type',
                        'expiresIn',
                    ],
                ],
            ]);
    }

    public function test_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'authorisation' => [
                        'token',
                        'type',
                        'expiresIn',
                    ],
                ],
            ]);
    }
}
