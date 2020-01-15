<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.11.17
 * Time: 11:58
 */

namespace api\components;

use yii\web\HttpException;

/**
 * Class Controller - Api common controller behavior and functionality
 * @package api\components
 */
class Controller extends \yii\web\Controller
{

    /**
     * Throw exception and set error code, error message and http response code
     * @param int $code Error code - must be described in ErrorCode class
     * @param string|bool $message Error message. If FALSE - will be set to default by error code (look to ErrorCode class)
     * @param int|bool $httpCode Http response code. If FALSE will be set to default by error code (look to ErrorCode class)
     *
     * @throws HttpException
     */
    public function throwError($code, $message = false, $httpCode = 500){
        \Yii::$app->response->throwError($code, $message, $httpCode);
    }

}