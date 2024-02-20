<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Campaign extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/campaigns';
        $this->setToken($options['token'] ?? 'system');
    }
    public function getContacts($params) {
        $arrContacts = \Microservices::Crm('Campaign')->detail($params);
        if(!empty($arrContacts) && !empty($arrContacts['contacts'])) {
            return array_unique($arrContacts['contacts']);
        }
        return [];
    }
}
