<?php

namespace Microservices\Caches\Test;

class Question extends \Microservices\Caches\BaseCache
{
    protected $service = 'test';
    protected $table = 'question';
    public function __construct($options = []) {
    }
}

