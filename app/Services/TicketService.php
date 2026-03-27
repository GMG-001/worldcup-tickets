<?php

namespace App\Services;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
    ) {
    }

    public function getByUser(int $userId): LengthAwarePaginator
    {
        return $this->repository->getByUser($userId);
    }

    public function show(int $id): Ticket
    {
        return $this->repository->findWithRelations($id);
    }

    public function create(int $userId, array $data): Ticket
    {
        return $this->repository->create([
            'user_id' => $userId,
            ...$data,
        ]);
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        return $this->repository->update($ticket, $data);
    }

    public function delete(Ticket $ticket): void
    {
        $this->repository->delete($ticket);
    }
}
