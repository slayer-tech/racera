<?php

namespace Tests\Feature;

use App\Models\Clan;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClanTest extends TestCase
{
    use RefreshDatabase, withFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetClans()
    {
        $user = User::factory()->create();
        Clan::factory([
            'creator_id' => $user->id
        ])->count(16)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/clans');

        $response->assertStatus(200)
                 ->assertJsonCount(15, 'data');

        $response = $this->actingAs($user)
                         ->getJson('/api/clans?page=2');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testShowClan()
    {
        $user = User::factory()->create();
        $profile = Profile::find($user->id);
        $clan = Clan::factory([
            'creator_id' => $user->id
        ])->create();

        $profile->clan_id = $clan->id;
        $profile->save();

        $response = $this->actingAs($user)
                         ->getJson('/api/clans/' . $clan->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'description',
                     'avatar',
                     'creator_id',
                     'profiles'
                 ]);
    }

    public function testUpdateClanData()
    {
        $user = User::factory()->create();
        $clan = Clan::factory()->create([
            'creator_id' => $user->id
        ]);

        $response = $this->actingAs($user)
                         ->patchJson('/api/clans/' . $clan->id, [
                             'name' => 'New Name',
                             'description' => 'New Description'
                         ]);

        $response->assertJson([
            'name' => 'New Name',
            'description' => 'New Description',
            'avatar' => $clan->avatar
        ]);
    }

    public function testStoreClan()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/clans/', [
                             'name' => $this->faker->name(),
                             'description' => $this->faker->text(),
                             'avatar' => $this->faker->imageUrl(),
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'description',
                     'avatar'
                 ])
                 ->assertJson([
                     'creator_id' => $user->id,
                 ]);
    }

    public function testSearchClan()
    {
        $user = User::factory()->create();
        Clan::factory()->create([
            'name' => 'First Clan',
            'creator_id' => $user->id
        ]);
        Clan::factory()->create([
            'name' => 'Second Clan',
            'creator_id' => $user->id
        ]);

        $response = $this->actingAs($user)
                         ->getJson('/api/clans/search/' . 'irst');

        $response->assertStatus(200)
                 ->assertJsonCount(1);

        $response = $this->actingAs($user)
                         ->getJson('/api/clans/search/' . 'econd');

        $response->assertStatus(200)
                 ->assertJsonCount(1);

        $response = $this->actingAs($user)
                         ->getJson('/api/clans/search/' . 'clan');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }
}
