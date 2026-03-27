<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService extends BaseService
{
    public function __construct(
        private TicketRepositoryInterface $repository,
    ) {
    }

    public function getByUser(int $userId): LengthAwarePaginator
    {
        return $this->repository->getByUser($userId);
    }

    public function show(int $id): Ticket
    {
        $ticket = $this->repository->findWithRelations($id);

        $this->authorize('view', $ticket);

        return $ticket;
    }
}
