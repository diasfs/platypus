<?php

namespace Platypus;

use Platypus\Http\Request;

class Router
{
    public static function parse($path)
    {
        $path = preg_replace('/\{([A-z-]+)\:([^\}]+)\}/', '(?<$1>$2)', $path);
        $path = preg_replace('/\{([A-z-]+)\}/', '(?<$1>[A-z0-9_-]+)', $path);

        $path = "#^{$path}/?$#";
        return $path;
    }

    public static function route($path, $callable)
    {
        $path = static::parse($path);
        $path_info = Request::PathInfo();
        if (preg_match($path, $path_info, $context)) {
            return call_user_func_array($callable, array($context));
        }
        return null;
    }
}
