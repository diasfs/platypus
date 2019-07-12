<?php

namespace Platypus\Http;

class Response
{
    public static function json($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_APOS);
    }

    public static function html($data)
    {
        header("Content-Type: text/html; charset=utf-8");
        echo $data;
    }
}
