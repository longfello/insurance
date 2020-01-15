<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 05.12.17
 * Time: 12:18
 */

namespace api\components;


class Request extends \yii\web\Request
{
    public $ipHeaders = [
        'HTTP_CLIENT_IP',
        'X-Forwarded-For',
        'REMOTE_ADDR',
    ];

    public function getUserIP(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), null))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), null))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), null))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], null))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ip = null;
        }
        return $ip;
    }
}