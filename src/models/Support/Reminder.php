<?php

namespace Microservices\models\Support;

use Illuminate\Support\Arr;

class Reminder extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/reminder';
        
    }

    public function completed($id)
    {
        $response = \Http::acceptJson()->withToken($this->getToken())->post(env('API_MICROSERVICE_URL_V2') . '/support/reminder/'.$id.'/completed');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return false;
    }
}
