<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class ContactDocument extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/contact-document';
        $this->setToken($options['token'] ?? 'system');
    }
}
