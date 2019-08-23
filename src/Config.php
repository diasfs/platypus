<?php

namespace Platypus;

use Jasny\DotKey;

class Config
{
    public static $data = [];
    public static $dot = null;

    public static function load($path)
    {
        $data = (array) require($path);
        static::$data = array_merge_recursive(static::$data, $data);
        static::$dot = new DotKey($data);
    }

    public static function getDot()
    {
        if (null == static::$dot) {
            static::$dot = new DotKey(array());
        }
        return static::$dot;
    }

    public static function get($key, $default = null)
    {
        $dot = static::getDot();
        return $dot->get($key, $default);
    }

    public static function set($key, $value = null)
    {
        $dot = static::getDot();
        static::$data = $dot->set($key, $value);
    }
}
