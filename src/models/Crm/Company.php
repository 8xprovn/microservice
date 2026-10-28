<?php

namespace Microservices\models\Crm;

use Illuminate\Support\Arr;

class Company extends \Microservices\models\Model
{
    protected $_url;
    //protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/crm/company';
    }

    public function addContact($param = [])
    {
        $url = $this->_url . "/add_contact";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }

    public function deleteContact($param = [])
    {
        $url = $this->_url . "/delete_contact";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
