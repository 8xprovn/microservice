<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class ClassSchedule extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/class-schedules';
        
    }

    public function semesterTest($id, $params)
    {
        $url = $this->_url . '/' . $id . '/semester-test';
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        $responseJson = $response->json();
        if (isset($responseJson['message'])) {
            return $responseJson;
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
    public function cancelSemesterTest($id, $params)
    {
        $url = $this->_url . '/' . $id . '/cancel-semester-test';
        $response = \Http::acceptJson()->withToken($this->access_token)->POST($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        $responseJson = $response->json();
        if (isset($responseJson['message'])) {
            return $responseJson;
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
