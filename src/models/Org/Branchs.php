<?php

namespace Models\Org;

use Illuminate\Support\Arr;

class Branchs extends \Models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL').'/org';
        $this->setToken($options['token'] ?? 'system');
    }
    protected $prefix = 'brand-branches';
}
