<?php

namespace Tests\Feature\Reservation;

use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReserveTest extends TestCase
{
    use RefreshDatabase;

    public function testFanCanReserveTickets(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create(['available_count' => 10]);

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 2,
            'idempotency_key'    => Str::uuid()->toString(),
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'ticket_category_id', 'quantity', 'status', 'expires_at'],
            ])
            ->assertJson([
                'data' => [
                    'ticket_category_id' => $category->getId(),
                    'quantity'           => 2,
                    'status'             => 'pending',
                ],
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id'            => $fan->getId(),
            'ticket_category_id' => $category->getId(),
            'quantity'           => 2,
            'status'             => 'pending',
        ]);
    }

    public function testReserveDecrementsAvailableCount(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create(['available_count' => 10]);

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 3,
            'idempotency_key'    => Str::uuid()->toString(),
        ])->assertStatus(201);

        $this->assertDatabaseHas('ticket_categories', [
            'id'              => $category->getId(),
            'available_count' => 7,
        ]);
    }

    public function testReserveRequiresAuthentication(): void
    {
        $this->postJson('/api/reservations', [])->assertStatus(401);
    }

    public function testAdminCannotReserve(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/reservations', [])->assertStatus(403);
    }

    public function testReserveFailsWhenNoSeatsAvailable(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create(['available_count' => 0]);

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 1,
            'idempotency_key'    => Str::uuid()->toString(),
        ])->assertStatus(422);
    }

    public function testReserveRequiresValidFields(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->postJson('/api/reservations', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ticket_category_id', 'quantity', 'idempotency_key']);
    }

    public function testReserveRejectsQuantityAboveFour(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create(['available_count' => 100]);

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 5,
            'idempotency_key'    => Str::uuid()->toString(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function testReserveRejectsDuplicateIdempotencyKey(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create(['available_count' => 100]);
        $key      = Str::uuid()->toString();

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 1,
            'idempotency_key'    => $key,
        ])->assertStatus(201);

        $this->actingAs($fan)->postJson('/api/reservations', [
            'ticket_category_id' => $category->getId(),
            'quantity'           => 1,
            'idempotency_key'    => $key,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['idempotency_key']);
    }
}
