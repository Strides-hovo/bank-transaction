<?php

namespace App\Services;

class Cache
{
    static $cacheKey = 'Y-m';
    public static function get($key)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        if (file_exists($FILE_PATH)){
            $month = date(self::$cacheKey);
            $data = json_decode(file_get_contents(dirname(__DIR__) . '/cache.json'), true);
            if (isset($data[$month][$key])){
                return $data[$month][$key];
            }
            return null;
        }
        return false;
    }

    public static function set($key, $value)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        $month = date(self::$cacheKey);
        if (!file_exists($FILE_PATH)){
            file_put_contents(dirname(__DIR__) . '/cache.json', json_encode([$month => [$key => $value]]));
        }
        else{
            $data = json_decode(file_get_contents($FILE_PATH), true);
            $data[$month][$key] = $value;
            file_put_contents(dirname(__DIR__) . '/cache.json', json_encode($data));
        }
    }

    public static function has($key)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        $month = date(self::$cacheKey);
        if (!file_exists($FILE_PATH)){
            return false;
        }
        $file = json_decode( file_get_contents($FILE_PATH), true);
        return isset($file[$month][$key]);

    }
}