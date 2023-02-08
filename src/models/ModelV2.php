<?php
namespace Models;

abstract class ModelV2
{
    public function all($filters = [], $options = [])
    {
        $q = ($filters) ? ['filter' => $filters] : '';  
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/'.$this->prefix, $q);
        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}
