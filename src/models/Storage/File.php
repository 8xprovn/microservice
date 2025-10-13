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
        $pathTrim = trim($path, '/');
        $arrs = explode('/', $pathTrim);

        if (empty($arrs)) return '/';
        $channel = env('UPLOAD_CHANNEL', '');
        $configDomainChannel = config("storage.{$channel}");
        $diskUpload = env('UPLOAD_DISK', trim($arrs[0] ?? ''));

        if (empty($configDomainChannel)) return env('SERVICE_MEDIA_URL', '') . '/' . trim($path, '/');
        $disk = $configDomainChannel[$diskUpload] ?? [];

        $configDomain = $disk['url'] ?? env('SERVICE_MEDIA_URL', '');
        $configDriver = $disk['driver'] ?? '';

        switch ($configDriver) {
            case "onedrive":
                $input = array_merge($params, ['path' => $path]);
                return "{$this->url}/files?" . http_build_query($input);
            default:
                return $configDomain . '/' . trim($path, '/');
        }
    }
}
