<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan("db:seed", ['--class=CategorySeeder']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetUpgrades()
    {
        $user = User::factory()->create();
        Upgrade::factory()->count(25)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/upgrades');

        $response->assertStatus(200)
                 ->assertJsonStructure([[
                     'id',
                     'name',
                     'description',
                     'price',
                     'bonus_id'
                 ]])
                 ->assertJsonCount(25);
    }

    public function testBuyUpgrade()
    {
        $upgrade = Upgrade::factory()->create();
        $user = User::factory()->create();
        $profile = Profile::find($user->id);

        $profile->update([
            'money' => 1000000
        ]);

        $response = $this->actingAs($user)
                         ->postJson('/api/upgrades/' . $upgrade->id);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message'
                 ]);
    }

    public function testSellUpgrade()
    {
        $upgrade = Upgrade::factory()->create();
        $user = User::factory()->create();
        $profile = Profile::find($user->id);

        $profile->update([
            'money' => 1000000
        ]);

        $response = $this->actingAs($user)
                         ->deleteJson('/api/upgrades/' . $upgrade->id);

        $response->assertStatus(400);

        $this->actingAs($user)
             ->postJson('/api/upgrades/' . $upgrade->id);

        $response = $this->actingAs($user)
                         ->deleteJson('/api/upgrades/' . $upgrade->id);

        $response->assertStatus(204);
    }
}
