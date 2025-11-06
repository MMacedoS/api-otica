<?php

namespace App\Config;

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

    }
}
