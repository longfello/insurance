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

use api\components\Response;
use yii\base\Component;
use yii\helpers\VarDumper;
use yii\web\HtmlResponseFormatter;

/**
 * HtmlResponseFormatter formats the given data into an HTML response content.
 * @inheritdoc
 */
class HtmlFormatter extends HtmlResponseFormatter
{
    /**
     * @inheritdoc
     */
    public function format($response)
    {
        if (!is_string($response->data)){
            $response->data = VarDumper::dumpAsString($response->data, 10, true);
        }
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $response->charset;
        }
        $response->getHeaders()->set('Content-Type', $this->contentType);
        if ($response->data !== null) {
            $response->content = $response->data;
        }
    }
}
