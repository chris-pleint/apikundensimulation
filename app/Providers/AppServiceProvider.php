<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Logging\LogCallback;
use Domainrobot\Domainrobot;
use Domainrobot\Lib\DomainrobotAuth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            'Domainrobot',
            function ($app) {
                return $this->getDomainrobot(
                    env('DOMAINROBOT_URL'),
                    env('DOMAINROBOT_USER'),
                    env('DOMAINROBOT_PASSWORD'),
                    env('DOMAINROBOT_CONTEXT')
                );
            }
        );

        $this->app->bind(
            'DomainrobotSSL',
            function ($app) {
                return $this->getDomainrobot(
                    env('DOMAINROBOT_URL'),
                    env('DOMAINROBOT_SSL_USER'),
                    env('DOMAINROBOT_SSL_PASSWORD'),
                    env('DOMAINROBOT_SSL_CONTEXT')
                );
            }
        );
    }

    /**
     * Create an Domainrobot Instance
     * 
     * @param  string $url
     * @param  string $user
     * @param  string $pass
     * @param  string $context
     * @return string $csrOut
     */
    protected function getDomainrobot($url, $user, $pass, $context) {

        $domainrobot = new Domainrobot([
            'url' => $url,
            'auth' => new DomainrobotAuth([
                'user' => $user,
                'password' => $pass,
                'context' => $context
            ]),
            'logRequestCallback' => function ($method, $url, $requestOptions, $headers){
                LogCallback::dailyRequest($method, $url, $requestOptions, $headers);
            },
            'logResponseCallback' => function ($url, $response, $statusCode, $exectime){
                LogCallback::dailyResponse($url, $response, $statusCode, $exectime);
            }
        ]);

        return $domainrobot;
    }
}
