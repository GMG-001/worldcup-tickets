<?php

namespace App\Repositories;

use App\Exceptions\InsufficientSeatsException;
use App\Models\TicketCategory;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryRepository implements TicketCategoryRepositoryInterface
{
    public function lockForUpdate(int $id): TicketCategory
    {
        $model = $this->getModel();

        return $model->lockForUpdate()->findOrFail($id);
    }

    public function decrementAvailableOrFail(int $id, int $amount): void
    {
        $model = $this->getModel();

        $model->lockForUpdate()->findOrFail($id);

        $affected = $model->where('id', $id)
            ->where('available_count', '>=', $amount)
            ->decrement('available_count', $amount);

        if ($affected === 0) {
            throw new InsufficientSeatsException();
        }
    }

    public function incrementAvailable(int $id, int $amount): void
    {
        $model = $this->getModel();

        $model->where('id', $id)->increment('available_count', $amount);
    }

    public function getModel(): TicketCategory
    {
        return new TicketCategory();
    }
}
