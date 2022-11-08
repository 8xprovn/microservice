<?php
namespace Models;

use MongoDB\Operation\FindOneAndUpdate;

abstract class Model
{
    public function all($params = [], $options = [])
    {
        $filter = [];
        foreach($params as $k => $v){
            if (is_null($v)) continue;
            switch ($k) {
                default:
                    if (is_array($v)) {
                        $filter[$k] = ['inq' => $v];
                    }
                    else {
                        $filter[$k] = ['eq' => $v];
                    }
                    break;
            }
        }
        $q = '';
        $q = ($filter) ? ['filter' => json_encode([
            'where' => $filter
        ])] : '';  
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/'.$this->prefix, $q);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
