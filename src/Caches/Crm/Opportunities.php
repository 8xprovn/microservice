<?php

namespace Microservices\Caches\Crm;

class Opportunities extends \Microservices\Caches\BaseCache
{
    protected $service = 'crm';
    protected $table = 'opportunities';
    public function __construct($options = []) {
    }
}
