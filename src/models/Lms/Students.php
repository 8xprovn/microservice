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
        $data = $this->safeGet($this->_url, $q, (string) $this->getToken());
        if ($data) {
            return collect($data)->pluck('contact_id')->unique()->values()->all();
        }
        return [];
    }
    public function examTime($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/student' . "/$id/exam_time";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function in_tutoring($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/students' . "/$id/in_tutoring";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function sendResult($id,array $params)
    {
        $url = env('API_MICROSERVICE_URL_V2').'/lms/class' . "/$id/student/send_result";
        $result = $this->safePost($url, $params, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
