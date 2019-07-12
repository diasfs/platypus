<?php

namespace Platypus;

use Camel\CaseTransformer;
use Camel\Format;

class App
{

    public static function init($config_path, $loader)
    {
        static::setup($config_path, $loader);
        static::start();
    }

    public static function setup($config_path, $loader)
    {
        Config::load($config_path . '/config/app.php');
        $path = Config::get('app.path');
        $loader->addPsr4("", "{$path}");
        $cache = Config::get('twig.cache');
        Twig::init(array("{$path}/views", "{$path}/assets"), $cache);
    }

    public static function start()
    {
        $languages = Config::get('languages', array('pt-br'));
        if (1 < count($languages)) {
            Router::route("/{language}?/?{controller}?/?{action}?/?(?<params>.+)?", array('\\Platypus\\App', 'route'));
        } else {
            Router::route("/{controller}?/?{action}?/?(?<params>.+)?", array('\\Platypus\\App', 'route'));
        }
    }

    public static function route($context)
    {
        $language = isset($context['language']) ? $context['language'] : 'pt-br';
        Config::set('language', $language);

        $controller = isset($context['controller']) ? $context['controller'] : 'home';
        $action = isset($context['action']) ? $context['action'] : 'index';
        $params = isset($context['params']) ? $context['params'] : '';
        $params = explode('/', trim($params, '/'));
        $params = array_filter($params, function ($param) {
            return '' != $param;
        });

        $transform = new CaseTransformer(new Format\SpinalCase, new Format\StudlyCaps);
        $controllerClass = '\\Controllers\\' . $transform->transform($controller);
        $actionMethod = $transform->transform($action);

        if (!class_exists($controllerClass)) {
            $controllerClass = '\\Controllers\\DefaultController';
        }

        if (!method_exists($controllerClass, $actionMethod)) {
            $actionMethod = 'Index';
        }

        $request = new Http\Request();
        $request->language = $language;
        $request->controller = $controller;
        $request->action = $action;
        $request->params = $params;

        $response = new Http\Response();

        call_user_func_array(array($controllerClass, $actionMethod), array($request, $response, $context));
    }
}
