<?php
namespace Microservices\Models;

abstract class ModelV2
{
    public function all($params = [], $options = [])
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
        $q = ['filter' => $filter];
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/'.$this->prefix, $q);
        if ($response->successful()) {
            return $response->json();
        } 
        \Log::error($response->body());
        return false;
    }
}
