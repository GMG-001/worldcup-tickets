<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Models\FootballMatch;
use App\Models\TicketCategory;
use App\Repositories\Interfaces\FootballMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FootballMatchRepository implements FootballMatchRepositoryInterface
{
    public function allWithCategories(): LengthAwarePaginator
    {
        $model = $this->getModel();

        return $model->with('ticketCategories')
            ->orderBy('match_date')
            ->paginate(15);
    }

    public function findOrFail(int $id): FootballMatch
    {
        $model = $this->getModel();

        return $model->with('ticketCategories')->findOrFail($id);
    }

    public function create(array $data): FootballMatch
    {
        $model = $this->getModel();

        return $model->create($data);
    }

    public function update(FootballMatch $match, array $data): FootballMatch
    {
        $match->update($data);

        return $match;
    }

    public function getReportStats(int $matchId): Collection
    {
        return TicketCategory::withCount([
            'tickets as tickets_sold' => fn ($q) => $q->where('status', TicketStatus::Issued),
        ])
            ->where('match_id', $matchId)
            ->get()
            ->each(function (TicketCategory $category): void {
                $category->revenue = $category->tickets_sold * $category->getPrice();
            });
    }

    public function getModel(): FootballMatch
    {
        return new FootballMatch();
    }
}
