<?php

namespace Platypus;

class Twig
{
    public static $twig;

    public static function init($templatesPaths, $templatesCache = false, $debug = false)
    {
        if (is_string($templatesCache) && !file_exists($templatesCache)) {
            mkdir($templatesCache, 0777, true);
        }

        foreach ($templatesPaths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($templatesPaths), [
            'debug' => $debug,
            'cache' => $templatesCache
        ]);
        static::$twig = $twig;

        return $twig;
    }

    public static function render($name, array $data = [])
    {
        $twig = static::$twig;
        if (is_null($twig)) {
            throw new \RuntimeException('Twig should be initialized first');
        }

        $data = array_merge_recursive([
            'config' => (array) Config::$dot
        ], $data);

        return $twig->render($name, $data);
    }
}
