<?php
namespace Microservices\Facade;

use Illuminate\Support\Facades\Facade;

class Microservices extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'Microservices'; 
    }
    public function load($class) {
        
        return new $className;
    }
    // public function 
    public function __call($method,$arg = []) {
        $func = array_shift($arg);
        $className = '\Microservices\models\\'.$method.'\\'.$func;
        return new $className($arg);
    }
}