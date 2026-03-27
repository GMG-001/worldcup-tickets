<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Repositories\Interfaces\FootballMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FootballMatchService
{
    public function __construct(
        private readonly FootballMatchRepositoryInterface $repository,
    ) {
    }

    public function index(): LengthAwarePaginator
    {
        return $this->repository->allWithCategories();
    }

    public function findOrFail(int $id): FootballMatch
    {
        return $this->repository->findOrFail($id);
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

    public function report(FootballMatch $match): array
    {
        $categories = $this->repository->getReportStats($match->getId());

        return [
            'match'         => $match,
            'total_revenue' => $categories->sum('revenue'),
            'categories'    => $categories,
        ];
    }
}
