<?php
namespace Microservices;

class FinanceV2
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/finance';
    }

    public function getInvoices($params = array())
    {
         $whereArr = \Arr::only($params, ['filter','page','limit']); 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices',$params);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
    }
    public function getInvoicesDetail($params = array())
    {
         $params = \Arr::only($params, ['filter','page','limit']); 
         $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/invoices-detail',$params);
         if ($response->successful()) {
             return $response->json();
         }
         \Log::error($response->body());
         return false;
    }

}