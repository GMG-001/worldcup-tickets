<?php

namespace App\Repositories\Interfaces;

use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Collection;

interface TicketCategoryRepositoryInterface
{
    public function all(): Collection;

    public function lockForUpdate(int $id): TicketCategory;

    public function decrementAvailableOrFail(int $id, int $amount): void;

    public function incrementAvailable(int $id, int $amount): void;

    public function create(array $data): TicketCategory;

    public function update(TicketCategory $category, array $data): TicketCategory;

    public function delete(TicketCategory $category): void;
}
