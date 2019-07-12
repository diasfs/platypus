<?php

namespace Platypus;

use Adbar\Dot;

class Config
{
    public static $data = [];
    public static $dot = null;

    public static function getDot()
    {
        if (null == static::$dot) {
            static::$dot = new Dot(array());
        }
        return static::$dot;
    }

    public static function load($path)
    {
        $dot = static::getDot();
        $data = (array) require($path);
        $dot->mergeRecursive($data);

        static::$data = $dot->all();
    }

    public static function get($key, $default = null)
    {
        $dot = static::getDot();
        return $dot->get($key, $default);
    }

    public static function set($key, $value = null)
    {
        $dot = static::getDot();
        $dot->set($key, $value);
        static::$data = $dot->all();
    }
}
