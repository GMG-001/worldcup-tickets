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

class IndexTest extends TestCase
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

    public function testFanCanListOwnTickets(): void
    {
        $fan = User::factory()->create();
        $this->createTicketForUser($fan);
        $this->createTicketForUser($fan);

        $this->actingAs($fan)->getJson('/api/tickets')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'reservation_id', 'match_id', 'category_id', 'seat_number', 'status', 'qr_code'],
                ],
                'meta',
                'links',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function testIndexOnlyReturnsOwnTickets(): void
    {
        $fan1 = User::factory()->create();
        $fan2 = User::factory()->create();

        $this->createTicketForUser($fan1);
        $this->createTicketForUser($fan1);
        $this->createTicketForUser($fan2);

        $this->actingAs($fan1)->getJson('/api/tickets')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testIndexReturnsEmptyListWhenNoTickets(): void
    {
        $fan = User::factory()->create();

        $this->actingAs($fan)->getJson('/api/tickets')
            ->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function testIndexRequiresAuthentication(): void
    {
        $this->getJson('/api/tickets')->assertStatus(401);
    }

    public function testAdminCannotListTickets(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/tickets')->assertStatus(403);
    }
}
