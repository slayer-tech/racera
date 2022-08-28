<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetCars()
    {
        Car::factory()->count(16)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                          ->getJson('/api/cars?page=1');

        $response->assertStatus(200)
                 ->assertJsonCount(15, 'data');

        $response = $this->actingAs($user)
                         ->getJson('/api/cars?page=2');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function testShowCar()
    {
        $car = Car::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/cars/' . $car->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id'
                 ]);
    }

    public function testBuyCar()
    {
        $car = Car::factory()->create();
        $user = User::factory()->create();
        $profile = Profile::find($user->id);

        $profile->update([
            'money' => 1000000
        ]);

        $response = $this->actingAs($user)
                         ->postJson('/api/cars/' . $car->id);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message'
                 ]);
    }

    public function testSellCar()
    {
        $car = Car::factory()->create();
        $user = User::factory()->create();
        $profile = Profile::find($user->id);

        $profile->money = 1000000;

        $profile->save();

        $this->actingAs($user)
             ->postJson('/api/cars/' . $car->id);

        $response = $this->actingAs($user)
                         ->deleteJson('/api/cars/' . $car->id);

        $response->assertStatus(204);
    }
}
