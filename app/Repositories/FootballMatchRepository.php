<?php

namespace App\Repositories;

use App\Models\FootballMatch;
use App\Repositories\Interfaces\FootballMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FootballMatchRepository implements FootballMatchRepositoryInterface
{
    public function all(): Collection
    {
        return FootballMatch::all();
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
}
