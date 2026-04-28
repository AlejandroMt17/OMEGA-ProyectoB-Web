<?php

namespace App\Providers;

use App\Repositories\Contracts\InstitucionRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\InstitucionRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(InstitucionRepositoryInterface::class, InstitucionRepository::class);
    }

    public function boot(): void
    {
    }
}