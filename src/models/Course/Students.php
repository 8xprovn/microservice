<?php

namespace Microservices\models\Course;

use Illuminate\Support\Arr;

class Students  extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = [])
    {
        $this->_url = env('API_MICROSERVICE_URL_V2') . '/course/students';
        $this->setToken($options['token'] ?? 'system');
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
        $response = \Http::acceptJson()->withToken($this->access_token)->get($url, $q);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
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
        $response = \Http::acceptJson()->withToken($this->access_token)->get($url, $q);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }

    public function tracking($id, $param = [])
    {
        $url = $this->_url . "/$id/tracking";

        $response = \Http::acceptJson()->withToken($this->access_token)->post($url, $param);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
    
    public function register_course($param = [])
    {
        $url = $this->_url . "/register-course";

        $response = \Http::acceptJson()->withToken($this->access_token)->post($url, $param);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($this->_url . $response->body());
        return [];
    }
}
