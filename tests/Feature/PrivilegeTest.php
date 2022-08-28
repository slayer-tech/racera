<?php

namespace Tests\Feature;

use App\Models\Privilege;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PrivilegeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetPrivileges()
    {
        $user = User::factory()->create();
        Privilege::factory()->count(25)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/privileges');

        $response->assertStatus(200)
                 ->assertJsonCount(25);
    }

    public function testShowPrivileges()
    {
        $user = User::factory()->create();
        $privilege = Privilege::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/privileges/' . $privilege->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'description',
                     'priority'
                 ]);
    }
}
