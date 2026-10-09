<?php

namespace Microservices\models\Inventory;

class Transaction extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/inventory/transaction';
        
    }

    public function transaction_detail($transaction_id, $options = []) 
    {
        $url = env('API_MICROSERVICE_URL_V2')."/inventory/transaction-detail/{$transaction_id}";
        try { 
            $result = $this->safeGet($url, [], (string) $this->getToken());
            return $result === false ? [] : $result;
        }catch (\Exception $ex){
            return [];
        } 
    }
}
