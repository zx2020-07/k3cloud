<?php


return [
    'host_url' => env('K3CLOUD_HOST_URL', ''), //金蝶授权请求地址
    'auth_type' => env('K3CLOUD_AUTH_TYPE', 1), //授权类型：1用户名+密码；2 第三方授权应用ID+应用密钥；3签名；
    'acct_id' => env('K3CLOUD_ACCT_ID', ''),  //账户ID
    'username' => env('K3CLOUD_USERNAME', ''), // 用户名（授权类型为1时必须）
    'password' => env('K3CLOUD_PASSWORD', ''), // 密码（授权类型为1时必须）
    'appid' => env('K3CLOUD_APPID', ''), // 应用ID（授权类型为2或3时必须）
    'appsecret' => env('K3CLOUD_APPSECRET', ''),  // 应用Secret（授权类型为2或3时必须）
    'lcid' => env('K3CLOUD_LCID', 2052),// 账套语系，默认2052
];
