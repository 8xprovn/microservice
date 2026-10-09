<?php

namespace Microservices\models\Course;

use Illuminate\Support\Arr;

class Students  extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/course/students';
        
    }
    public function list_classes($params = [], $options = [])
    {
        $url = env('API_MICROSERVICE_URL_V2') . '/course/class_to_contact';
        $filter = [];
        foreach ($params as $k => $v) {
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $result = $this->safeGet($url, $q, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function list_courses($params = [], $options = [])
    {
        $url = env('API_MICROSERVICE_URL_V2') . '/course/course_to_contact';
        $filter = [];
        foreach ($params as $k => $v) {
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    $filter[$k] = $v;
                    break;
            }
        }
        $q = $options;
        $q['filter'] = $filter;
        $result = $this->safeGet($url, $q, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function tracking($id, $param = [])
    {
        $url = $this->_url . "/$id/tracking";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
    
    public function register_course($param = [])
    {
        $url = $this->_url . "/register-course";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
