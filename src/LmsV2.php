<?php
namespace Microservices;


class LmsV2
{
    protected $_url;
    protected $_hash_secret;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/lms';
        $this->_hash_secret = env('TEST_HASH_SECRET');
    }

    //COURSE PRICE
    public function getCoursePrices($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/course-prices',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    //COURSE PRICE
    public function getClasses($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/classes',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function getCourses($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/courses',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function getStudentIncludeClass($params = array()) {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/students', $params);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
    public function createLicense($input) {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN', ''))->post($this->_url.'/license', $input);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
