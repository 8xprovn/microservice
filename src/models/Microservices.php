<?php
namespace Microservices\models;

class Microservices
{
    public function loadCache($classLoad,$arg = []) {
        $className = '\Microservices\Caches\\'.$classLoad;
        return new $className($arg);
    }
    // public function 
    public function __call($method,$arg = []) {
        $func = array_shift($arg);
        $className = '\Microservices\models\\'.$method.'\\'.$func;
        return new $className($arg);
    }
}