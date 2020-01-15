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
use yii\web\XmlResponseFormatter;

/**
 * Class XmlFormatter
 * @inheritdoc
 * @package api\components\formatters
 */
class XmlFormatter extends XmlResponseFormatter
{
    /**
     * @inheritdoc
     * @var string
     */
    public $rootTag = 'items';

}