<?php

namespace Tests\Feature\Reservation;

use App\Enums\ReservationStatus;
use App\Jobs\ProcessPaymentJob;
use App\Models\Reservation;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PayTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingReservation(User $user, int $categoryId): Reservation
    {
        return Reservation::create([
            'user_id'            => $user->getId(),
            'ticket_category_id' => $categoryId,
            'quantity'           => 1,
            'status'             => ReservationStatus::Pending,
            'expires_at'         => now()->addMinutes(10),
            'idempotency_key'    => Str::uuid()->toString(),
        ]);
    }

    public function testFanCanPayForReservation(): void
    {
        Queue::fake();

        $fan         = User::factory()->create();
        $category    = TicketCategory::factory()->create();
        $reservation = $this->createPendingReservation($fan, $category->getId());

        $this->actingAs($fan)->postJson("/api/reservations/{$reservation->getId()}/pay")
            ->assertStatus(202)
            ->assertJson(['message' => 'Payment is being processed.']);

        Queue::assertPushed(ProcessPaymentJob::class);
    }

    public function testPayRequiresAuthentication(): void
    {
        $fan         = User::factory()->create();
        $category    = TicketCategory::factory()->create();
        $reservation = $this->createPendingReservation($fan, $category->getId());

        $this->postJson("/api/reservations/{$reservation->getId()}/pay")
            ->assertStatus(401);
    }

    public function testFanCannotPayOthersReservation(): void
    {
        $owner       = User::factory()->create();
        $other       = User::factory()->create();
        $category    = TicketCategory::factory()->create();
        $reservation = $this->createPendingReservation($owner, $category->getId());

        $this->actingAs($other)->postJson("/api/reservations/{$reservation->getId()}/pay")
            ->assertStatus(403);
    }

    public function testPayReturns404ForNonExistentReservation(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->postJson('/api/reservations/999/pay')
            ->assertStatus(404);
    }

    public function testPayFailsForExpiredReservation(): void
    {
        $fan      = User::factory()->create();
        $category = TicketCategory::factory()->create();

        $reservation = Reservation::create([
            'user_id'            => $fan->getId(),
            'ticket_category_id' => $category->getId(),
            'quantity'           => 1,
            'status'             => ReservationStatus::Pending,
            'expires_at'         => now()->subMinute(),
            'idempotency_key'    => Str::uuid()->toString(),
        ]);

        $this->actingAs($fan)->postJson("/api/reservations/{$reservation->getId()}/pay")
            ->assertStatus(422)
            ->assertJson(['message' => 'Reservation has expired.']);
    }
}
