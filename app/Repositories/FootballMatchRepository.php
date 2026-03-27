<?php

namespace App\Repositories;

use App\Models\FootballMatch;
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
        return FootballMatch::create($data);
    }

    public function update(FootballMatch $match, array $data): FootballMatch
    {
        $match->update($data);

        return $match;
    }

    public function delete(FootballMatch $match): void
    {
        $match->delete();
    }

    public function getModel(): FootballMatch
    {
        return new FootballMatch();
    }
}
