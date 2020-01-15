<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.11.17
 * Time: 16:16
 */

namespace api\components\formatters;


use yii\base\Component;
use yii\web\JsonResponseFormatter;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * Class JsonFormatter
 * @inheritdoc
 * @package api\components\formatters
 */
class JsonFormatter extends JsonResponseFormatter
{
    /**
     * @inheritdoc
     * @var bool
     */
    public $prettyPrint = true;

}