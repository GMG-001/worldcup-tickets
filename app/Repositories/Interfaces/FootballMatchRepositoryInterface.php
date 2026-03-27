<?php

namespace App\Repositories\Interfaces;

use App\Models\FootballMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface FootballMatchRepositoryInterface
{
    public function allWithCategories(): LengthAwarePaginator;

    public function findOrFail(int $id): FootballMatch;

    public function create(array $data): FootballMatch;

    public function update(FootballMatch $match, array $data): FootballMatch;

    public function delete(FootballMatch $match): void;
}
