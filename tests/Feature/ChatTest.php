<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetChats()
    {
        $user = User::factory()->create();

        $chat = Chat::factory()->create();

        $chat->profiles()->attach($user->id);
        $chat->profiles()->attach($user->id);

        $response = $this->actingAs($user)
                         ->getJson('/api/chats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     "data" => [
                         "$user->id" => [
                             "id",
                             "name",
                             "avatar",
                         ]
                     ]
                 ]);
    }

    public function testShowChat()
    {
        $user = User::factory()->create();
        $chat = Chat::factory()->create();

        $chat->profiles()->attach($user->id);
        $chat->profiles()->attach($user->id);

        $response = $this->actingAs($user)
            ->getJson('/api/chats/' . $user->id);

        $this->actingAs($user)
             ->postJson('/api/messages', [
                 'recipient_id' => $user->id,
                 'content' => $this->faker->text(),
             ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "recipient" => [
                        "id",
                        "name",
                        "avatar",
                    ],
                    "messages"
                ]
            ]);
    }
}
