<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Segments extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/segments';
        $this->setToken($options['token'] ?? 'system');
    }
    public function getContacts($params) {
        $segment = \Microservices::Crm('Segments')->detail($params);
        if (!empty($segment['data'])) {
            $contact_ids = collect(\Microservices::Crm('Contacts')->all(json_decode(json_decode($segment['data'])->filter, true), ['limit' => 500]))->pluck('_id')->toArray();
            if(!empty($contact_ids)) {
                return array_unique($contact_ids);
            }
        }
        return [];
    }
}
