<?php

namespace App\Repositories\Interfaces;

use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Collection;

interface TicketCategoryRepositoryInterface
{
    public function lockForUpdate(int $id): TicketCategory;

    public function decrementAvailableOrFail(int $id, int $amount): void;

    public function incrementAvailable(int $id, int $amount): void;
}
