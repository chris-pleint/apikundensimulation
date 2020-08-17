<?php

namespace App\Http\Controllers;

use App\Logging\LogCallback;
use Domainrobot\Domainrobot;
use Domainrobot\Lib\DomainrobotAuth;

class SslController extends Controller
{
    protected function getDomainrobot() 
    {
        $domainrobot = new Domainrobot([
            "url" => env('DOMAINROBOT_URL'),
            "auth" => new DomainrobotAuth([
                "user" => env('DOMAINROBOT_SSL_USER'),
                "password" => env('DOMAINROBOT_SSL_PASSWORD'),
                "context" => env('DOMAINROBOT_SSL_CONTEXT')
            ]),
            "logRequestCallback" => function ($method, $url, $requestOptions, $headers){
                LogCallback::dailyRequest($method, $url, $requestOptions, $headers);
            },
            "logResponseCallback" => function ($url, $response, $statusCode, $exectime){
                LogCallback::dailyResponse($url, $response, $statusCode, $exectime);
            }
        ]);

        return $domainrobot;
    }
}
