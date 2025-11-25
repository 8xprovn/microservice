<?php

namespace Microservices\Caches\Test;

class Test extends \Microservices\Caches\BaseCache
{
    protected $service = 'test';
    protected $table = 'test';
    public function __construct($options = []) {
    }
}

