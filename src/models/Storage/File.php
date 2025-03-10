<?php

namespace Microservices\models\Storage;

use Illuminate\Support\Facades\Http;

class File
{
    protected $url;
    protected $hash;
    public function __construct()
    {
        $this->url = env('SERVICE_STORAGE_URL', 'https://storage.ebomb.edu.vn/api');
        $this->hash = env('SERVICE_STORAGE_HASH_SECRET', '123456');
    }
    public function view($params)
    { 
        $url = "{$this->url}/show-files";
        $response = Http::get($url, $params); 
        if ($response->successful()) {
            return $response->json();
        }
        return [];
    }
    public function token($string, $config_key_md5 = '')
    {
        if (empty($config_key_md5)) $config_key_md5 = $this->hash;
        if (is_array($string)) {
            $string = implode('-', $string);
        }
        return md5("{$config_key_md5}_{$string}");
    }
}
