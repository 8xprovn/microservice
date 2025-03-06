<?php

namespace Microservices\models\Security;

class Url
{
    public function md5_code($string, $config_key_md5 = '')
    {
        if (empty($config_key_md5)) $config_key_md5 = env('SECURITY_VIEW_URL_MD5');
        if (is_array($string)){
            $string = implode('-', $string);
        }
        return md5("{$config_key_md5}_{$string}");
    }
}
