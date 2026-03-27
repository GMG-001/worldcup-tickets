<?php

namespace App\Repositories;

use App\Exceptions\InsufficientSeatsException;
use App\Models\TicketCategory;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryRepository implements TicketCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return TicketCategory::with('match')->get();
    }

    public function lockForUpdate(int $id): TicketCategory
    {
        return TicketCategory::lockForUpdate()->findOrFail($id);
    }

    public function decrementAvailableOrFail(int $id, int $amount): void
    {
        TicketCategory::lockForUpdate()->findOrFail($id);

        $affected = TicketCategory::where('id', $id)
            ->where('available_count', '>=', $amount)
            ->decrement('available_count', $amount);

        if ($affected === 0) {
            throw new InsufficientSeatsException();
        }
    }

    public function incrementAvailable(int $id, int $amount): void
    {
        TicketCategory::where('id', $id)->increment('available_count', $amount);
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
