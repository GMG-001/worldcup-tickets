<?php

namespace Tests\Feature\Reservation;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CancelTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingReservation(User $user, TicketCategory $category, int $quantity = 1): Reservation
    {
        return Reservation::create([
            'user_id'            => $user->getId(),
            'ticket_category_id' => $category->getId(),
            'quantity'           => $quantity,
            'status'             => ReservationStatus::Pending,
            'expires_at'         => now()->addMinutes(10),
            'idempotency_key'    => Str::uuid()->toString(),
        ]);
    }

    public function testFanCanCancelReservation(): void
    {
        $fan         = User::factory()->create();
        $category    = TicketCategory::factory()->create(['available_count' => 5]);
        $reservation = $this->createPendingReservation($fan, $category, 2);

        $this->actingAs($fan)->deleteJson("/api/reservations/{$reservation->getId()}")
            ->assertStatus(204);

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->getId(),
            'status' => 'cancelled',
        ]);
    }

    public function testCancelRestoresAvailableCount(): void
    {
        $fan         = User::factory()->create();
        $category    = TicketCategory::factory()->create(['available_count' => 5]);
        $reservation = $this->createPendingReservation($fan, $category, 3);

        $this->actingAs($fan)->deleteJson("/api/reservations/{$reservation->getId()}")
            ->assertStatus(204);

        $this->assertDatabaseHas('ticket_categories', [
            'id'              => $category->getId(),
            'available_count' => 8,
        ]);
    }

    public function testCancelRequiresAuthentication(): void
    {
        $fan         = User::factory()->create();
        $category    = TicketCategory::factory()->create();
        $reservation = $this->createPendingReservation($fan, $category);

        $this->deleteJson("/api/reservations/{$reservation->getId()}")
            ->assertStatus(401);
    }

    public function testFanCannotCancelOthersReservation(): void
    {
        $owner       = User::factory()->create();
        $other       = User::factory()->create();
        $category    = TicketCategory::factory()->create();
        $reservation = $this->createPendingReservation($owner, $category);

        $this->actingAs($other)->deleteJson("/api/reservations/{$reservation->getId()}")
            ->assertStatus(403);
    }

    public function testCancelReturns404ForNonExistentReservation(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->deleteJson('/api/reservations/999')
            ->assertStatus(404);
    }
}
