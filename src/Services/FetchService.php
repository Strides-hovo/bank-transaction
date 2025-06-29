<?php

namespace App\Services;

class FetchService
{
    public static function fetchRateToCHF($currency)
    {
        $apiKey = '9c939dba92545858a6cf81d5';

        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$currency}/CHF";
        $resp = json_decode(file_get_contents($url), true);

        return $resp['conversion_rate'];
    }
}