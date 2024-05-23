<?php

namespace Microservices\models\Notification;

use Illuminate\Support\Arr;

class App extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/notification/type/app';
        $this->setToken($options['token'] ?? 'system');
    }

    public function updateReadStatus($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = env('API_MICROSERVICE_URL_V2') . '/notification/app/is_read/' . $id;
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($url . $response->body());
        return false;
    }
}
