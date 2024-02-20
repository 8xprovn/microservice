<?php

namespace Microservices\models\Lms;

use Illuminate\Support\Arr;

class Students extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/lms/students';
        $this->setToken($options['token'] ?? 'system');
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
        $response = \Http::acceptJson()->withToken($this->access_token)->get($this->_url, $q);
        if ($response->successful()) {
            $contact_ids = collect($response->json())->pluck('contact_id')->unique()->values()->all();
            return $contact_ids;
        } 
        \Log::error($this->_url . $response->body());
        return [];
    }
}
