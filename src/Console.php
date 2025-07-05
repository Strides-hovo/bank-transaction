<?php

namespace App;

class Console
{

    /**
     * @var array
     */
    private $tokens;
    private $methodName;

    public function __construct(array $argv = [])
    {

        array_shift($argv);
        if (in_array('down', $argv)){
            $index = array_search('down', $argv);
            $this->methodName = $argv[$index];
            unset($argv[$index]);
        }
        else{

        }

        $this->tokens = $argv;

    }


    public function terminate()
    {
        $infos = [];
        foreach ($this->tokens as $token) {
            $classes = $this->getClasses($token);
            $callMethod = $this->getCallMethod($token);

            foreach ($classes as $class) {
                if (class_exists($class)) {
                   $infos[] = (new $class())->$callMethod();
                }
            }
        }
        return $infos;
    }


    private function getClasses($key)
    {
        $namespace = "\\App\\" . ucfirst($key);
        $classes = array_diff(scandir("src/" . ucfirst($key)), array('..', '.'));
        return array_map(function ($class) use($namespace) {
            return "{$namespace}\\" .rtrim($class,'.php');
        }, $classes);
    }

    private function getCallMethod($key)
    {
        if ($this->methodName !== null){
            return $this->methodName;
        }
        if ($key == 'migrate'){
            return 'up';
        }
        return null;
    }
}