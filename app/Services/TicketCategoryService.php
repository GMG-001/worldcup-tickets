<?php

namespace App\Services;

use App\Models\TicketCategory;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryService
{
    public function __construct(
        private readonly TicketCategoryRepositoryInterface $repository,
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): TicketCategory
    {
        return $this->repository->create($data);
    }

    public function update(TicketCategory $category, array $data): TicketCategory
    {
        return $this->repository->update($category, $data);
    }

    public function delete(TicketCategory $category): void
    {
        $this->repository->delete($category);
    }
}