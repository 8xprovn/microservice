<?php

namespace Microservices\Caches\Uploads;

class DropboxToken extends \Microservices\Caches\BaseCache
{
    protected $service = 'uploads';
    protected $table = 'dropbox_token';
}

