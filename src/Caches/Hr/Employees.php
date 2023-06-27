<?php

namespace Microservices\Caches\Hr;

class Employees extends \Microservices\Caches\BaseCache
{
    protected $service = 'hr';
    protected $table = 'employee';
    public function __construct($options = []) {
    }
}

