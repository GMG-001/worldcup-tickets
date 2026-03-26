<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Repositories\Interfaces\FootballMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FootballMatchService
{
    public function __construct(
        private readonly FootballMatchRepositoryInterface $repository,
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): FootballMatch
    {
        return $this->repository->create($data);
    }

    public function update(FootballMatch $match, array $data): FootballMatch
    {
        return $this->repository->update($match, $data);
    }

    public function delete(FootballMatch $match): void
    {
        $this->repository->delete($match);
    }
}
