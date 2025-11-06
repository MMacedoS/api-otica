<?php

namespace App\Config;

use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Entities\Users\UsuarioRepository;

class AppServiceProvider
{
    public function __construct(Container $container)
    {
        $this->registerServices($container);
    }

    public static function registerServices(Container $container)
    {
        // Register services here
        // Example:
        // $container->set(SomeService::class, new SomeService());

        $container->set(IUsuarioRepository::class, new UsuarioRepository());
    }
}
