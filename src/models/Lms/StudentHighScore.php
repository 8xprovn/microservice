<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class StudentHighScore extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/highscore';
        
    }

    public function storeTicket($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/student/highscore' . "/$id/log";
        $response = \Http::acceptJson()->withToken($this->getToken())->post($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        
        if (!empty($response->json())) {
            return $response->json();
        }

        \Log::error($url . $response->body());
        return [];
    }
}
