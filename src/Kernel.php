<?php

namespace App;

class Kernel
{

    protected $providers = [];
    protected $aliases = [
        'db' => DB::class,
        'console' => Console::class,
        'migrate' => Migrate::class
    ];

    public function __construct()
    {
    }


    public function bind(array $binds)
    {
       $keys = array_keys($binds);
       $this->providers[$keys[0]] = $binds[$keys[0]];
    }


    public function setAlias()
    {
        foreach ($this->aliases as $key => $alias) {
             if (key_exists($alias, $this->providers)){
                 $this->providers[$key] = $this->providers[$alias];
             }
        }
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return array_merge($this->providers, $this->aliases);
    }
}