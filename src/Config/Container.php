<?php

namespace App\Config;

use App\Utils\LoggerHelper;
use ReflectionClass;

class Container
{
    protected $services = [];
    protected $provider = null;

    public function __construct()
    {
        $this->provider = new AppServiceProvider($this);
    }

    public function set($name, $service)
    {
        $this->services[$name] = $service;
    }

    public function get($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        if (class_exists($name)) {
            $reflection = new ReflectionClass($name);
            $constructor = $reflection->getConstructor();

            if (is_null($constructor)) {
                $instance = $reflection->newInstance();
                return $this->services[$name] = $instance;
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependencyClass = $parameter->getType()->getName();
                $dependencies[] = $this->get($dependencyClass);
            }

            return $this->services[$name] = $reflection->newInstanceArgs($dependencies);
        }
        LoggerHelper::logError("Service {$name} not found in container.");
        throw new \Exception("Service {$name} not found.");
    }
}
