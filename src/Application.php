<?php

namespace App;

use ReflectionClass;
use ReflectionException;

class Application
{

    private $container;
    private $app;


    public function __construct()
    {
        $this->app = App::getInstance();
    }

    public function run()
    {

    }

    /**
     * @throws ReflectionException
     */
public function setContainer($abstract, $params = array())
{
    if (!isset($this->container[$abstract])) {
        $this->container[$abstract] = $this->resolveClass($abstract, $params);
    }
}

/**
 * @throws ReflectionException
 */
private function resolveClass($abstract, $params = array())
{

    $ref = new ReflectionClass($abstract);

    if (!$ref->isInstantiable()) {
        return $abstract;
    }

    $construct = $ref->getConstructor();
    $parameters = $construct ? $construct->getParameters() : array();
    if (empty($parameters)) {
        return $ref->newInstanceArgs($params);
    }
    if (!empty($params)){
        return $ref->newInstance($params);
    }
    $deps = array();

    foreach ($parameters as $parameter) {
        $class = $parameter->getClass();
        if ($class) {
            $deps[] = $this->resolveClass($class->getName());
        } else {
            $deps[] = isset($params[$parameter->getName()]) ? $params[$parameter->getName()] : null;
        }
    }

    return $ref->newInstanceArgs($deps);
}

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }
}