<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\FootballMatchRepository;
use App\Repositories\Interfaces\FootballMatchRepositoryInterface;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\TicketCategoryRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ReservationRepository;
use App\Repositories\TicketCategoryRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class BindingServiceProviders extends ServiceProvider
{
    private const REPOSITORIES = [
        FootballMatchRepositoryInterface::class  => FootballMatchRepository::class,
        ReservationRepositoryInterface::class    => ReservationRepository::class,
        TicketRepositoryInterface::class         => TicketRepository::class,
        TicketCategoryRepositoryInterface::class => TicketCategoryRepository::class,
        UserRepositoryInterface::class           => UserRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORIES as $interface => $repository) {
            $this->app->bind($interface, $repository);
        }
    }

    public function boot(): void
    {
        //
    }
}
