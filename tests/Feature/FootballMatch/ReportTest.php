<?php

namespace Tests\Feature\FootballMatch;

use App\Models\FootballMatch;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminCanViewReport(): void
    {
        $admin = User::factory()->admin()->create();
        $match = FootballMatch::factory()->create();
        TicketCategory::factory()->count(2)->create(['match_id' => $match->getId()]);

        $this->actingAs($admin)->getJson("/api/admin/matches/{$match->getId()}/report")
            ->assertStatus(200)
            ->assertJsonStructure(['match', 'total_revenue', 'categories']);
    }

    public function testReportRequiresAuthentication(): void
    {
        $match = FootballMatch::factory()->create();

        $this->getJson("/api/admin/matches/{$match->getId()}/report")
            ->assertStatus(401);
    }

    public function testFanCannotViewReport(): void
    {
        $fan   = User::factory()->create();
        $match = FootballMatch::factory()->create();

        $this->actingAs($fan)->getJson("/api/admin/matches/{$match->getId()}/report")
            ->assertStatus(403);
    }

    public function testReportReturns404ForNonExistentMatch(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/admin/matches/999/report')
            ->assertStatus(404);
    }

    public function testReportShowsZeroRevenueWhenNoTicketsSold(): void
    {
        $admin    = User::factory()->admin()->create();
        $match    = FootballMatch::factory()->create();
        TicketCategory::factory()->create(['match_id' => $match->getId()]);

        $this->actingAs($admin)->getJson("/api/admin/matches/{$match->getId()}/report")
            ->assertStatus(200)
            ->assertJson(['total_revenue' => 0]);
    }
}
