<?php

namespace Glitter\K3Cloud\Core;

use Exception;
use Glitter\K3Cloud\Exceptions\HttpException;
use Glitter\K3Cloud\Traits\HttpClientTraits;
use GuzzleHttp\Exception\GuzzleException;

class WebApiClient
{
    use HttpClientTraits;

    /**
     * @param $url
     * @param $headers
     * @param $postData
     * @param $format
     * @return mixed
     * @throws HttpException|GuzzleException
     */
    public function execute($url, $headers, $postData, $format)
    {
        try {
            $headers = $this->defaultHeaders + $headers;
            $response = $this->httpClient->post(
                $url,
                $this->defaultGuzzleOption + [
                    'headers' => $headers,
                    'json' => $postData
                ]
            );
            $res = $response->getBody()->getContents();
            return $format == 'string' ? $res : json_decode($res, true);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function buildHeader($serviceUrl, $config): array
    {
        //$path_url = parse_url($serviceUrl, PHP_URL_PATH);
        $path_url = str_replace('/', '%2F', $serviceUrl);
        $timestamp = time();    // 1643627187
        $nonce = $timestamp;
        $arr = explode('_', $config['appid']);
        $client_id = $arr[0];
        $client_sec = $this->decodeAppSecret($arr[1]);
        $apiSign = 'POST\n' . $path_url . '\n\nx-api-nonce:' . $nonce . '\nx-api-timestamp:' . $timestamp . '\n';
        $appData = $config['acct_id'] . ',' . $config['username'] . ',' . ($this->config['lcid'] ?? 2052) . ',' . ($this->config['org_num'] ?? 0);
        return [
            'X-Api-Auth-Version' => '2.0',
            'X-Api-ClientID' => $client_id,
            'x-api-timestamp' => $timestamp,
            'x-api-nonce' => $nonce,
            'x-api-signheaders' => 'x-api-timestamp,x-api-nonce',
            'X-Api-Signature' => $this->kdHmacSHA256($apiSign, $client_sec),
            'X-Kd-Appkey' => $config['appid'],
            'X-Kd-Appdata' => base64_encode($appData),
            'X-Kd-Signature' => $this->kdHmacSHA256($config['appid'] . $appData, $config['appsecret']),
        ];
    }

    protected function kdHmacSHA256($content, $sign_key): string
    {
        $signature = hash_hmac('sha256', $content, $sign_key, true);
        $sign_hex = bin2hex($signature);
        return base64_encode($sign_hex);
    }

    protected function decodeAppSecret($secret)
    {
        if (strlen($secret) != 32) {
            return $secret;
        }
        $xor_code = '0054f397c6234378b09ca7d3e5debce7';             // example from official Java SDK
        $base64_decode = base64_decode($secret);
        $base64_xor = $base64_decode ^ $xor_code;
        return base64_encode($base64_xor);
    }
}
