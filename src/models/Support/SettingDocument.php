<?php

namespace Microservices\models\Support;


class SettingDocument extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/setting_document';
        $this->setToken($options['token'] ?? 'system');
    }
}
