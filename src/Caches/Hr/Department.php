<?php

namespace Microservices\Caches\Hr;

class Department extends \Microservices\Caches\BaseCache
{
    protected $service = 'hr';
    protected $table = 'employee_department';
    public function __construct($options = []) {}
}
