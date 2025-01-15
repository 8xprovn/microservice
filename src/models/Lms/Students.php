<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class Students extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/students';
        
    }

    public function getContactId($params = [], $options = [])
    {
        $filter = [];
        foreach($params as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $response = \Http::acceptJson()->withToken($this->getToken())->get($this->_url, $q);
        if ($response->successful()) {
            $contact_ids = collect($response->json())->pluck('contact_id')->unique()->values()->all();
            return $contact_ids;
        } 
        \Log::error($this->_url . $response->body());
        return [];
    }
    public function examTime($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/student' . "/$id/exam_time";
        $response = \Http::acceptJson()->withToken($this->getToken())->post($url, $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($url . $response->body());
        return [];
    }

    public function in_tutoring($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/students' . "/$id/in_tutoring";
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
