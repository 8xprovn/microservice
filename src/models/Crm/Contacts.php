<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Contacts extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/contacts';
        $this->setToken($options['token'] ?? 'system');
    }
    public function find($phoneOrEmail,$options = array()) {
        $options = array_merge($options,['limit' => 1]);
        if (filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->all(['filter' => ['email' => $phoneOrEmail]],$options);
        }
        else {
            $phoneOrEmail = preg_replace('/^0/', '+84', $phoneOrEmail);
            return $this->all(['filter' => ['phone' => $phoneOrEmail]],$options);
        }
    }
}
