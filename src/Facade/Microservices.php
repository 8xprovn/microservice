<?php
namespace Microservices\Facade;

use Illuminate\Support\Facades\Facade;

class Microservices extends Facade 
{
    protected static function getFacadeAccessor() { 
        return 'Microservices'; 
    }
}