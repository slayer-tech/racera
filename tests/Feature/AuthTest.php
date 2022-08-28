<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseStatusCodeSame;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $login = 'John';
    protected string $email = 'john@gmail.com';
    protected string $password = 'john123';

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function register(string $login = null,
                                string $email = null,
                                string $password = null) : TestResponse
    {
        return $this->postJson('/api/auth/signup',
            [
                'login' => $login ?? $this->login ,
                'email' => $email ?? $this->email ,
                'password' => $password ?? $this->password
            ]
        );
    }

    protected function login(string $login = null,
                             string $password = null) : TestResponse
    {
        return $this->postJson('/api/auth/login',
            [
                'login' => $login ?? $this->login ,
                'password' => $password ?? $this->password
            ]
        );
    }

    protected function createUser()
    {
        return User::factory()->create([
            'password' => bcrypt('john123')
        ]);
    }

    public function testSignup() : void
    {
        $response = $this->register();

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson(['token' => true]);
    }

    public function testSignupFailureForbidden()
    {
        $response = $this->register();
        $response = $this->register();

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testLoginWithLogin() : void
    {
        $user = $this->createUser();
        $response = $this->login($user->login);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['token' => true]);
    }

    public function testLoginWithEmail() : void
    {
        $user = $this->createUser();
        $response = $this->login($user->email);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['token' => true]);
    }

    public function testLoginFailureUnprocessableEntity()
    {
        $this->createUser();
        $response = $this->login('notworkinglogin');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLoginFailureForbidden()
    {
        $user = $this->createUser();
        $this->login($user->email);
        $response = $this->login($user->email);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testLogout() : void
    {
        $this->login();

        $response = $this->actingAs($this->user)
                         ->getJson('/api/auth/logout');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'message' => true
                 ]);
    }

    public function testGetUser() : void
    {
        $response = $this->actingAs($this->user)
                         ->getJson('/api/auth/user');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'id' => true,
                     'login' => true,
                     'email' => true
                 ]);
    }
}
