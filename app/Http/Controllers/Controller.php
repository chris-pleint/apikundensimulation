<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Domainrobot\Domainrobot;
use Domainrobot\Lib\DomainrobotAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getDomainrobot() 
    {
        $domainrobot = new Domainrobot([
            "url" => env('DOMAINROBOT_URL'),
            "auth" => new DomainrobotAuth([
                "user" => env('DOMAINROBOT_USER'),
                "password" => env('DOMAINROBOT_PASSWORD'),
                "context" => env('DOMAINROBOT_CONTEXT')
            ])
        ]);

        return $domainrobot;
    }
}
