<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository implements TicketRepositoryInterface
{
    public function getByUser(int $userId): LengthAwarePaginator
    {
        return Ticket::with(['match', 'category'])
            ->where('user_id', $userId)
            ->paginate(15);
    }

    public function findWithRelations(int $id): Ticket
    {
        return Ticket::with(['match', 'category'])->findOrFail($id);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);

        return $ticket;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
    }
}
