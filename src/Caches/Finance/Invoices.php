<?php

namespace Microservices\Caches\Finance;

class Invoices extends \Microservices\Caches\BaseCache
{
    protected $service = 'finance';
    protected $table = 'invoices';
    public function __construct($options = []) {
    }
}

