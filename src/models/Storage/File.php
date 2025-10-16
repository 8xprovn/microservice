<?php

namespace Microservices\models\Storage;

use Illuminate\Support\Facades\Http;

class File
{
    protected $url;
    protected $hash;
    protected $_listener;
    protected $_service_code;
    public function __construct()
    {
        $this->url = env('SERVICE_STORAGE_URL', 'https://storage.ebomb.edu.vn/api');
        $this->hash = env('SERVICE_STORAGE_HASH_SECRET', '123456');
        $this->_listener = 'App\Jobs\MoveFileUpload';
        $this->_service_code = 'erp_system_backend_v2';
    }
    public function view($params)
    {
        $url = "{$this->url}/show-files";
        $response = Http::get($url, $params);
        $result =  ['status' => $response->status()];

        if ($response->successful()) {
            $result = array_merge($response->json(), $result);
        }
        return $result;
    }
    public function token($string, $config_key_md5 = '')
    {
        if (empty($config_key_md5)) $config_key_md5 = $this->hash;
        if (is_array($string)) {
            $string = implode('-', $string);
        }
        return md5("{$config_key_md5}_{$string}");
    }

    public function show($path, $params = [])
    {
        $arrs = explode('/', trim($path, '/'));
        if (empty($arrs)) return '/';
        $channel = env('UPLOAD_CHANNEL', trim($arrs[0] ?? ''));

        $configChannels = (array) json_decode(env("STORAGE_CHANNEL", '{}'));

        $configDriver = env("STORAGE_DISK", '');

        foreach ($configChannels as $key => $v) {
            if ((is_array($v) && in_array($channel, $v)) || (is_string($v) && $channel == $v)) {
                $configDriver = $key;
                break;
            }
        }
        switch ($configDriver) {
            case "onedrive":
                $input = array_merge($params, ['path' => $path]);
                return "{$this->url}/files?" . http_build_query($input);
            case "r2":
                return env('SERVICE_MEDIA_URL_R2', '') . '/' . trim($path, '/');
            default:
                return env('SERVICE_MEDIA_URL', '') . '/' . trim($path, '/');
        }
    }

    public function move($datas = [], $dataOlds = [])
    {
        $fileNews = $fileOlds = [];
        foreach ($datas as $file) {
            if (is_array($file)) {
                $fileNews = array_merge($fileNews, array_values($file));
            } else {
                $fileNews[] = $file;
            }
        }
        if (!empty($dataOlds)) {
            foreach ($dataOlds as $file) {
                if (is_array($file)) {
                    $fileOlds = array_merge($fileOlds, array_values($file));
                } else {
                    $fileOlds[] = $file;
                }
            }
        }
        $files =  array_diff($fileNews, $fileOlds);
        if (empty($files)) return;
        return \Microservices\Jobs\BusJob::dispatch($this->_listener, ['files' => $files])->onQueue($this->_service_code);
    }
}
