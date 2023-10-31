<?php

namespace Microservices\Caches\Uploads;

class Brands extends \Microservices\Caches\BaseCache
{
    protected $service = 'uploads';
    protected $table = 'dropbox_token';
}

