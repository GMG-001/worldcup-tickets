<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository implements TicketRepositoryInterface
{
    public function getByUser(int $userId): LengthAwarePaginator
    {
        $model = $this->getModel();

        return $model->with(['match', 'category'])
            ->where('user_id', $userId)
            ->paginate(15);
    }

    public function findWithRelations(int $id): Ticket
    {
        $model = $this->getModel();

        return $model->with(['match', 'category'])->findOrFail($id);
    }

    public function getModel(): Ticket
    {
        return new Ticket();
    }
}
