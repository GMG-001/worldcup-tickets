<?php

namespace App\Repositories;

use App\Models\TicketCategory;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryRepository implements TicketCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return TicketCategory::with('match')->get();
    }

    public function create(array $data): TicketCategory
    {
        return TicketCategory::create($data);
    }

    public function update(TicketCategory $category, array $data): TicketCategory
    {
        $category->update($data);

        return $category;
    }

    public function delete(TicketCategory $category): void
    {
        $category->delete();
    }
}