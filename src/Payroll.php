<?php
namespace Microservices;


class Payroll
{
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_URL').'/payroll';
    }

    public function getAllBenefitWithActive()
    {
        $filter = ['status' => 'active'];

        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->get($this->_url.'/benefits/',['filter' => json_encode(['where' => $filter])]);

        if ($response->successful()) {
            return $response->json();
        }
        \Log::error($response->body());
        return false;
    }
}