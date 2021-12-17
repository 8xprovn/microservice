<?php
namespace Microservices;

class Upload
{
    
    protected $_url;
    public function __construct() {
        $this->_url = env('API_MICROSERVICE_UPLOAD_URL');
    }

    public function moveFiles($source_path = null) {
        if(!$source_path) {
            return false;
        }
        
        $response = \Http::withToken(env('API_MICROSERVICE_TOKEN',''))->patch( $this->_url.'/move',[
            'source_path' => $source_path,
        ]);
        if ($response->successful()) {
            return $response->json();
        }

        \Log::error($response->body());
        return false;
    }
}