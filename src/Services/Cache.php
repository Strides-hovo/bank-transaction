<?php

namespace App\Services;

class Cache
{

    public static function get($key)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        if (file_exists($FILE_PATH)){
            return json_decode(file_get_contents(dirname(__DIR__) . '/cache.json'), true)[$key] ? :[];
        }
        return false;
    }

    public static function set($key, $value)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        if (!file_exists($FILE_PATH)){
            file_put_contents(dirname(__DIR__) . '/cache.json', json_encode([$key => $value]));
        }
        else{
            $data = json_decode(file_get_contents($FILE_PATH), true);
            $data[$key] = $value;
            file_put_contents(dirname(__DIR__) . '/cache.json', json_encode($data));
        }
    }

    public static function has($key)
    {
        $FILE_PATH = dirname(__DIR__) . '/cache.json';
        if (!file_exists($FILE_PATH)){
            return false;
        }
        $file = json_decode( file_get_contents($FILE_PATH), true);
        return isset($file[$key]);

    }
}