<?php

namespace Microservices\Caches\Hr;

class JobTitle extends \Microservices\Caches\BaseCache
{
    protected $service = 'hr';
    protected $table = 'employee_job_title';
    public function __construct($options = []) {
    }
}

