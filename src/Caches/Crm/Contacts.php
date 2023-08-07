<?php

namespace Microservices\Caches\Crm;

class Contacts extends \Microservices\Caches\BaseCache
{
    protected $service = 'crm';
    protected $table = 'contact';
    public function __construct($options = []) {
    }
}

