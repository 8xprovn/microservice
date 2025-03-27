<?php

namespace Microservices\Caches\CallCenter;

class Ticket extends \Microservices\Caches\BaseCache
{
    protected $service = 'callcenter';
    protected $table = 'tickets';
    public function __construct($options = []) {
    }
}

