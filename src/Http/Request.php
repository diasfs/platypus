<?php

namespace Platypus\Http;

function array_get(array $array, $key = null, $default = null, $caseInsensitive = false)
{
    if (is_null($key)) {
        return $array;
    }
    if ($caseInsensitive) {
        $array = array_change_key_case($array);
        $key = strtolower($key);
    }
    return array_key_exists($key, $array) ? $array[$key] : $default;
}

class Request
{
    public static function PathInfo()
    {
        $scriptName = array_get($_SERVER, 'SCRIPT_NAME', '');
        $queryString = array_get($_SERVER, 'QUERY_STRING', '');
        $requestUri = array_get($_SERVER, 'REQUEST_URI', '');
        $requestUri = str_replace('?' . $queryString, '', $requestUri);
        $scriptPath = str_replace('\\', '/', dirname($scriptName));

        if (!strlen(str_replace('/', '', $scriptPath))) {
            return '/' . ltrim($requestUri, '/');
        } else {
            return '/' . ltrim(str_replace(basename($scriptName), '', ltrim(str_replace($scriptPath, '', $requestUri), '/')), '/');
        }
    }

    public static function BaseUrl()
    {
        $queryString = array_get($_SERVER, 'QUERY_STRING', '');
        $requestUri = array_get($_SERVER, 'REQUEST_URI', '');
        $requestUri = str_replace('?' . $queryString, '', $requestUri);
        $requestUri = substr($requestUri, 0, -strlen(static::PathInfo()));


        $https = array_get($_SERVER, 'HTTP_X_FORWARDED_PROTO', array_get($_SERVER, 'HTTPS', ''));
        $protocol = empty($https) ? 'http' : 'https';

        $host = array_get($_SERVER, 'HTTP_X_FORWARDED_HOST', array_get($_SERVER, 'HTTP_HOST', ''));

        $requestUri = trim($requestUri, '/');

        $base_url = $protocol . '://' . trim($host . '/' . $requestUri, '/') . '/';

        return $base_url;
    }



    public static function raw($input = "php://input")
    {
        return (string) file_get_contents($input);
    }

    public static function json($input = 'php://input')
    {
        $params = json_decode(static::raw($input), true);
        if (is_null($params)) {
            return array();
        }
        return $params;
    }

    public static function post($key = null, $default = null)
    {
        return array_get($_POST, $key, $default);
    }

    public static function get($key = null, $default = null)
    {
        return array_get($_GET, $key, $default);
    }

    public static function input($key = null, $default = null)
    {
        return array_get($_REQUEST, $key, $default);
    }

    public static function cookie($key = null, $default = null)
    {
        return array_get($_COOKIE, $key, $default);
    }

    public static function session($key = null, $default = null)
    {
        return array_get($_SESSION, $key, $default);
    }

    public static function file($key = null, $default = null)
    {
        return array_get($_FILES, $key, $default);
    }

    public static function method()
    {
        if ($method = array_get($_POST, '_method')) {
            return $method;
        }
        if ($method = array_get($_SERVER, 'REQUEST_METHOD')) {
            return $method;
        }
        return 'GET';
    }

    public static function headers()
    {
        $serverKeys = array_keys($_SERVER);
        $httpHeaders = array_reduce($serverKeys, function ($headers, $key) {
            if ($key == 'CONTENT_TYPE') {
                $headers[] = $key;
            }
            if ($key == 'CONTENT_LENGTH') {
                $headers[] = $key;
            }
            if (substr($key, 0, 5) == 'HTTP_') {
                $headers[] = $key;
            }
            return $headers;
        }, array());
        $values = array_map(function ($header) {
            return $_SERVER[$header];
        }, $httpHeaders);
        $headers = array_map(function ($header) {
            if (substr($header, 0, 5) == 'HTTP_') {
                $header = substr($header, 5);
            }
            return str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $header))));
        }, $httpHeaders);
        return array_combine($headers, $values);
    }

    public static function header($key, $default = null)
    {
        return array_get(static::headers(), $key, $default, true);
    }
}
