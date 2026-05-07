<?php

namespace App\Providers;

use App\Repositories\AsistenciaRepository;
use App\Repositories\AuthRepository;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use App\Repositories\Contracts\InstitucionRepositoryInterface;
use App\Repositories\Contracts\RubroRepositoryInterface;
use App\Repositories\Contracts\SesionRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\GrupoRepository;
use App\Repositories\InstitucionRepository;
use App\Repositories\RubroRepository;
use App\Repositories\SesionRepository;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(InstitucionRepositoryInterface::class, InstitucionRepository::class);
        $this->app->bind(GrupoRepositoryInterface::class, GrupoRepository::class);
        $this->app->bind(SesionRepositoryInterface::class, SesionRepository::class);
        $this->app->bind(AsistenciaRepositoryInterface::class, AsistenciaRepository::class);
        $this->app->bind(RubroRepositoryInterface::class, RubroRepository::class);
    }

    public function boot(): void
    {
        //
    }
}