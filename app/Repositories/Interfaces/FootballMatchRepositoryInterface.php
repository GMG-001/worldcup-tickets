<?php

namespace App\Repositories\Interfaces;

use App\Models\FootballMatch;
use Illuminate\Database\Eloquent\Collection;

interface FootballMatchRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): FootballMatch;

    public function update(FootballMatch $match, array $data): FootballMatch;

    public function delete(FootballMatch $match): void;
}