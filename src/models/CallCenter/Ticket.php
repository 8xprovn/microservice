<?php

namespace Microservices\models\CallCenter;

use Illuminate\Support\Arr;

class Ticket extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/call-center/ticket';
        $this->setToken($options['token'] ?? 'system');
    }

    public function close($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/close';
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($url . $response->body());
        return false;
    }

    public function comment($id, $params)
    {
        if (!empty($this->only['update'])) {
            $params = \Arr::only($params, $this->only['update']);
        }
        if (!empty($this->idAutoIncrement)) {
            $id = (int) $id;
        }
        $url = $this->_url . '/' . $id . '/comment';
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($url . $response->body());
        return false;
    }
}

