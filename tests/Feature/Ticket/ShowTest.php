<?php

namespace Tests\Feature\Ticket;

use App\Enums\ReservationStatus;
use App\Enums\TicketStatus;
use App\Models\FootballMatch;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private function createTicketForUser(User $user): Ticket
    {
        $match    = FootballMatch::factory()->create();
        $category = TicketCategory::factory()->create(['match_id' => $match->getId()]);

        $reservation = Reservation::create([
            'user_id'            => $user->getId(),
            'ticket_category_id' => $category->getId(),
            'quantity'           => 1,
            'status'             => ReservationStatus::Confirmed,
            'expires_at'         => now()->addMinutes(10),
            'idempotency_key'    => Str::uuid()->toString(),
        ]);

        return Ticket::create([
            'user_id'        => $user->getId(),
            'reservation_id' => $reservation->getId(),
            'match_id'       => $match->getId(),
            'category_id'    => $category->getId(),
            'seat_number'    => 'A1',
            'status'         => TicketStatus::Issued,
            'qr_code'        => Str::uuid()->toString(),
        ]);
    }

    public function testFanCanViewOwnTicket(): void
    {
        $fan    = User::factory()->create();
        $ticket = $this->createTicketForUser($fan);

        $this->actingAs($fan)->getJson("/api/tickets/{$ticket->getId()}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id'          => $ticket->getId(),
                    'seat_number' => $ticket->getSeatNumber(),
                    'status'      => $ticket->getStatus()->value,
                    'qr_code'     => $ticket->getQrCode(),
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reservation_id',
                    'match_id',
                    'category_id',
                    'seat_number',
                    'status',
                    'qr_code',
                    'match',
                    'category'
                ],
            ]);
    }

    public function testShowRequiresAuthentication(): void
    {
        $fan    = User::factory()->create();
        $ticket = $this->createTicketForUser($fan);

        $this->getJson("/api/tickets/{$ticket->getId()}")->assertStatus(401);
    }

    public function testAdminCannotViewTicket(): void
    {
        $fan    = User::factory()->create();
        $admin  = User::factory()->admin()->create();
        $ticket = $this->createTicketForUser($fan);

        $this->actingAs($admin)->getJson("/api/tickets/{$ticket->getId()}")->assertStatus(403);
    }

    public function testFanCannotViewOthersTicket(): void
    {
        $owner  = User::factory()->create();
        $other  = User::factory()->create();
        $ticket = $this->createTicketForUser($owner);

        $this->actingAs($other)->getJson("/api/tickets/{$ticket->getId()}")->assertStatus(403);
    }

    public function testShowReturns404ForNonExistentTicket(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->getJson('/api/tickets/999')->assertStatus(404);
    }
}
