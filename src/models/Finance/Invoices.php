<?php

namespace Microservices\models\Finance;

class Invoices extends \Microservices\models\Model
{
    protected $_url;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/finance/invoices';
        
    }
    protected $service = 'erp_finance_backend_v2';
    protected $table = 'invoices';
    protected $is_cache = 1;

    public function send($id, $param = [])
    {
        $url = $this->_url . "/$id/send";
        $result = $this->safePost($url, $param, (string) $this->getToken());
        return $result === false ? [] : $result;
    }
}
