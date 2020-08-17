<?php

namespace App\Logging;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogCallback
{
    public static function dailyRequest($method, $url, $options, $user)
    {
        Log::channel('daily-request')->info(print_r(
            "[".(!empty($user->email)?$user->email:"n/a")."]". // user
            "[".(!empty($user->adns_user_id)?$user->adns_user_id:"n/a")."]". // adns user
            "[".app('Request')->getRequestType()."]". // requestType
            "[".app('realIP')."]". // ip
            "[".$method."]". // type
            "[".$url."]". // url
            "[".Carbon::now()->format('Y-m')."]". // month
            json_encode(array_filter($options, function ($value, $key) {
                if (in_array($key, array("auth", "on_stats", "timeout"))) {
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_BOTH)), // params
            true
        ));
    }

    public static function dailyResponse($url, $response, $statusCode, $exectime, $user)
    {
        Log::channel('daily-response')->info(print_r(
            "[".(!empty($user->email)?$user->email:"n/a")."]". // user
            "[".(!empty($user->adns_user_id)?$user->adns_user_id:"n/a")."]". // adns user
            "[".app('Request')->getRequestType()."]". // requestType
            "[".app('realIP')."]". // ip
            "[".$url."]". // url
            "[".$statusCode."]". // status
            "[".$exectime."]". // exectime
            "[".Carbon::now()->format('Y-m')."]". // month
            $response, // response
            true
        ));
    }
}
