<?php

namespace Microservices\models\Support;

use Illuminate\Support\Arr;

class Reminder extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/support/reminder';
        $this->setToken($options['token'] ?? 'system');
    }

    public function completed($id)
    {
        $response = \Http::acceptJson()->withToken($this->access_token)->post(env('API_MICROSERVICE_URL_V2') . '/support/reminder/'.$id.'/completed');
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return false;
    }
}
