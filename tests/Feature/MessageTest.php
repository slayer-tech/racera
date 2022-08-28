<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStoreMessage()
    {
        $user = User::factory()->create();
        $profile = Profile::find($user->id);

        $response = $this->actingAs($user)
                         ->postJson('/api/messages', [
                             'recipient_id' => $profile->id,
                             'content' => $this->faker->text(),
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'chat_id',
                     'profile_id',
                     'content'
                 ]);
    }
}
