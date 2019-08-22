<?php

namespace Platypus;

use RedBeanPHP\BeanHelper\SimpleFacadeBeanHelper as SimpleFacadeBeanHelper;

class PlatypusSimpleFacadeBeanHelper extends SimpleFacadeBeanHelper
{
    public function getModelForBean(\RedBeanPHP\OODBBean  $bean)
    {
        $model     = $bean->getMeta('type');
        $prefix    = defined('REDBEAN_MODEL_PREFIX') ? REDBEAN_MODEL_PREFIX : '\\Models\\RedBean\\';

        if (strpos($model, '_') !== FALSE) {
            $modelParts = explode('_', $model);
            $modelName = '';
            foreach ($modelParts as $part) {
                $modelName .= ucfirst($part);
            }
            $modelName = $prefix . $modelName;
            if (!class_exists($modelName)) {
                $modelName = $prefix . ucfirst($model);
                if (!class_exists($modelName)) {
                    $modelName = "\\Models\\RedBean\\DefaultModel";
                    if (!class_exists($modelName)) {
                        return NULL;
                    }
                }
            }
        } else {
            $modelName = $prefix . ucfirst($model);
            if (!class_exists($modelName)) {
                $modelName = "\\Models\\RedBean\\DefaultModel";
                if (!class_exists($modelName)) {
                    return NULL;
                }
            }
        }
        $obj = self::factory($modelName);
        $obj->loadBean($bean);
        return $obj;
    }
}

class R extends \RedBeanPHP\R
{ };



$type = Config::get('db.type', 'mysql');

if ('sqlite' == $type) {
    $path = Config::get('db.path');
    R::setup("sqlite:{$path}");
} else {
    $host = Config::get('db.host');
    $user = Config::get('db.user');
    $pass = Config::get('db.password');
    $dbname = Config::get('db.dbname');
    R::setup("{$type}:host={$host};dbname={$dbname}", $user, $pass);
}

R::getRedBean()->setBeanHelper(new PlatypusSimpleFacadeBeanHelper);

R::ext('xdispense', function ($type) {
    return R::getRedBean()->dispense($type);
});
