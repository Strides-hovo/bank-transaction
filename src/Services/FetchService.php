<?php

namespace App\Services;

class FetchService
{
    public static function fetchRateToCHF($currency)
    {
        $apiKey = '4564571e95c08de7c393ee32';
        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$currency}/CHF";
        try {
            if ( Cache::has('CHF') ){
                return Cache::get('CHF');
            }
            else{
                $course = json_decode(@file_get_contents($url), true);
                Cache::set('CHF', $course['conversion_rate']);
                return $course['conversion_rate'];
            }
        }
        catch (\Exception $ex){
            return 0;
        }
    }
}