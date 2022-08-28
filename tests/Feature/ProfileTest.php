<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testGetProfiles()
    {
        $user = User::factory()->create(); // The user factory also creates a profile record
        Profile::factory(15)->create(); // count = 16

        $response = $this->actingAs($user)
                         ->getJson('/api/profiles?page=1');

        $response->assertStatus(200)
                 ->assertJsonCount(15, 'data');


        $response = $this->actingAs($user)
                         ->getJson('/api/profiles?page=2');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function testShowProfile()
    {
        $profile = Profile::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/profiles/' . $profile->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id'
                 ]);
    }

    public function testUpdateProfile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->patchJson('/api/profiles/', [
                             'name' => 'John',
                             'description' => "I'm player",
                             'avatar' => '123.jpg'
                         ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message'
                 ]);
    }

    public function testSearchProfile()
    {
        $profile = Profile::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/profiles/search/' . substr($profile->name, 1, 5));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     "$profile->id" => [
                        'id'
                    ]
                 ]);
    }

}
