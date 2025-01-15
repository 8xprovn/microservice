<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Contacts extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/contacts';
        
    }
    public function find($phoneOrEmail,$options = array()) {
        $options = array_merge($options,['limit' => 1]);
        if (filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
            $contacts = $this->all(['emailphone' => $phoneOrEmail], $options);
        }
        else {
            $phoneOrEmail = preg_replace('/^0/', '+84', $phoneOrEmail);
            $contacts = $this->all(['emailphone' => $phoneOrEmail], $options);
        }
        return !empty($contacts) ? $contacts[0] : [];
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
            $contact_ids = collect($response->json())->pluck('_id')->unique()->values()->all();
            return $contact_ids;
        } 
        \Log::error($this->_url . $response->body());
        return [];
    }
}
