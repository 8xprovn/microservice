<?php

namespace Microservices\models\Integration;


class Qr
{
    private $md5Secret = 'Imap_123@';
    public function __construct($options = [])
    {
    }

    function create($string, $size = 250)
    {
        $_url = env('URL_QR') ?? 'https://s.ebomb.edu.vn/file/qr';
        $file = '';
        if (empty($string)) {
            return $file;
        }
        $namefile = md5($string.$this->md5Secret).'.png';
        $file = $_url.'/file/qr/'.$size.'/'.$namefile.'?string='.urlencode($string);
        return $file;
    }
}
