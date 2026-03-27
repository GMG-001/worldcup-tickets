<?php

namespace App\Repositories\Interfaces;

use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;

interface TicketRepositoryInterface
{
    public function getByUser(int $userId): LengthAwarePaginator;

    public function findWithRelations(int $id): Ticket;
}
