<?php

namespace App\Services;

use App\Models\TicketCategory;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryService extends BaseService
{
    public function __construct(
        private readonly TicketCategoryRepositoryInterface $repository,
    ) {
    }
}
