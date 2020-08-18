<?php

namespace App\Logging;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogCallback
{
    public static function dailyRequest($method, $url, $options, $headers)
    {
        Log::channel('daily-request')->info(print_r(
            "[".\json_encode($headers)."]". // headers
            "[".$method."]". // type
            "[".$url."]". // url
            "[".Carbon::now()->format('Y-m-d H:i:s')."]". // Datetime
            \json_encode(array_filter($options, function ($value, $key) {
                if (in_array($key, array("auth", "on_stats", "timeout"))) {
                    return false;
                }
                return true;
            }, ARRAY_FILTER_USE_BOTH)), // params
            true
        ));
    }

    public static function dailyResponse($url, $response, $statusCode, $exectime)
    {
        Log::channel('daily-response')->info(print_r(
            "[".$url."]". // url
            "[".$statusCode."]". // status
            "[".$exectime."]". // exectime
            "[".Carbon::now()->format('Y-m-d H:i:s')."]". // Datetime
            $response, // response
            true
        ));
    }
}
