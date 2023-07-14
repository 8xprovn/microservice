<?php
namespace Microservices;

class TestsV2
{
    protected $_url;
    protected $_hash_secret;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/tests';
        $this->_hash_secret = env('TEST_HASH_SECRET');
    }

    //Tests
    public function getTests($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTestDetail($id){
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/tests/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    //Questions
    public function getQuestions($params = array())
    {
        $whereArr = \Arr::only($params, ['filter','page','limit']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/questions',$params);
        if ($response->successful()) {
             return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getQuestionDetail($id){
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/questions/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function getTestLogDetail($id)
    {
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/test-logs/'.$id);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }

    public function updateTestLog($id, $params = []) {       
        $params = \Arr::only($params, ['score', 'contact_id', 'result', 'user_ans', 'review']);
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->patch($this->_url.'/test-logs/'.$id, $params);
        if ($response->successful()) {
            return true;
        }
        \Log::error($response->body());
        return false;
    }
}
