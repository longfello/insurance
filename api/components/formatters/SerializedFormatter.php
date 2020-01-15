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
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * Class SerializedFormatter
 * @inheritdoc
 * @package api\components\formatters
 */
class SerializedFormatter extends Component implements ResponseFormatterInterface
{
    /**
     * Formats the specified response.
     * @inheritdoc
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'text/plain; charset=UTF-8');
        // $response->getHeaders()->set('Content-Type', 'application/vnd.php.serialized; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = serialize($response->data);
        }
    }
}