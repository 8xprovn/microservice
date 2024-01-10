<?php

namespace Microservices\models\Inventory;

class Transaction extends \Microservices\models\Model
{
    protected $_url;
//    protected $is_cache = 1;
    public function __construct($options = []) {
        $this->_url = env('API_MICROSERVICE_URL_V2').'/inventory/transaction';
        $this->setToken($options['token'] ?? 'system');
    }

    public function transaction_detail($transaction_id, $options = []) 
    {
        $url = env('API_MICROSERVICE_URL_V2')."/inventory/transaction-detail/{$transaction_id}";
        try { 
            $response = \Http::acceptJson()->withToken($this->access_token)->get($url);
           
            if ($response->successful()) {
                return $response->json();
            }
            if($response->status() == 403){
                return ['status' => 'error' , 'message'  => 'UnAuthenticated'];
            }
            \Log::error($this->_url . $response->body());
            return [];
        }catch (\Exception $ex){
            return [];
        } 
    }
}
