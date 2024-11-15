<?php

namespace Glitter\K3Cloud\Traits;

use GuzzleHttp\Client;

trait HttpClientTraits
{
    private $defaultHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Accept-Charset' => 'utf-8',
        'User-Agent' => 'Kingdee/Php WebApi SDK (compatible: K3Cloud 7.3+)',
    ];

    private $defaultGuzzleOption = [
        'http_errors' => false,
    ];

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['cookies' => true]);
    }
}
