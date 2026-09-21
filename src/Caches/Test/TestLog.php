<?php

namespace Microservices\Caches\Test;

class TestLog extends \Microservices\Caches\BaseCache
{
    protected $service = 'test';
    protected $table = 'test_logs';
    public function __construct($options = []) {
    }
}

