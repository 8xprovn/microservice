<?php

namespace Microservices\models\Authorization;

class Permissions extends \Microservices\models\Model
{
    protected $_url;
    protected $prefix = 'permission';
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/authorization';
        if (!empty($options['token'])) {
            $this->setToken($options['token']);
        }
    }
    public function me($params)
    {
        $url = $this->_url.'/'.$this->prefix.'/me';
        $response = \Http::withToken($this->person_token)->get($url);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($url . $response->body());
        return false;
    }
}

