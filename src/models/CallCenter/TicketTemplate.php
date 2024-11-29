<?php

namespace Microservices\models\CallCenter;

class TicketTemplate extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center/ticket-template';
        $this->setToken($options['token'] ?? 'system');
    }
}

