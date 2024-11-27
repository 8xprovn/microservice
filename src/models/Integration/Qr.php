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
        $_url = env('URL_QR') ?? 'https://s.ebomb.edu.vn';
        $file = '';
        if (empty($string)) {
            return $file;
        }
        $md5String = md5($string.$this->md5Secret);
        $part1 = substr($md5String, 0, 2);  // "7b"
        $part2 = substr($md5String, 2, 2);  // "af"
        $part3 = substr($md5String, 4, 2);  // "c7"

        // Nối các phần lại với dấu "/"
        $folder = $part1 . '/' . $part2 . '/' . $part3;
        $namefile = $md5String.'.png';
        $file = $_url.'/file/qr/'.$folder.'/'.$size.'/'.$namefile.'?string='.urlencode($string);
        return $file;
    }
}
