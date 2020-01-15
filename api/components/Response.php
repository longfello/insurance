<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.11.17
 * Time: 16:13
 */

namespace api\components;

use yii\base\Event;
use yii\web\HttpException;

/**
 * Class Response
 * @package api\components
 */
class Response extends \yii\web\Response
{
    /**
     * Is Error occurred
     * @var bool
     */
    private $isError = false;

    /**
     * List of available response formats
     * @var array
     */
    public $availableFormats = [
        Response::FORMAT_JSON,
        Response::FORMAT_XML,
        Response::FORMAT_HTML,
//        Response::FORMAT_SERIALIZED,
    ];


    /**
     * Error code
     * @var int
     */
    public $errorCode = 0;
    /**
     * Error message
     * @var string
     */
    public $errorMessage = '';

    /**
     * Additional response format, not definde in parent Responce class
     */
    const FORMAT_SERIALIZED = 'serialized';

    /**
     * List of response formaters
     * @var array
     */
    public $formatters = [
        self::FORMAT_SERIALIZED => 'api\components\formatters\SerializedFormatter',
        self::FORMAT_JSON => 'api\components\formatters\JsonFormatter',
        self::FORMAT_HTML => 'api\components\formatters\HtmlFormatter',
        self::FORMAT_XML => 'api\components\formatters\XmlFormatter',
    ];

    /**
     * Component initialization - set event listeners && response format
     */
    public function init(){
        parent::init();
        $this->setFormat();
        $this->on('beforeSend', function ($event) {
            $response = $event->sender;
            /** @var $event Event */
            \Yii::$app->response->setFormat();

            if (!$response->isSuccessful) {
                $this->errorCode = $this->errorCode?$this->errorCode:ErrorCode::UNDEFINED_ERROR;

                $exception = \Yii::$app->errorHandler->exception;

                switch ($this->format) {
                    case Response::FORMAT_HTML:
                        $this->data = \Yii::$app->controller->render('error', ['exception' => $exception]);
                        break;
                    default:
                        $this->data =  [
                            'error' => [
                                'code'    => $this->errorCode ? $this->errorCode : $exception->getCode(),
                                'message' => $this->errorMessage ? $this->errorMessage : $exception->getMessage()
                            ]
                        ];
                }

                if (\Yii::$app->rest->handler && \Yii::$app->rest->handler->log ){
                    \Yii::$app->rest->handler->log->response = json_encode($this->data);
                    \Yii::$app->rest->handler->log->save();
                }

            }
        });

    }

    /**
     * Set response format
     * @throws HttpException Fired on bad response format given
     */
    public function setFormat(){
        if ($format = \Yii::$app->request->get('format')){
            if (in_array($format, $this->availableFormats)){
                $this->format = $format;
            } else {
                $this->throwError(ErrorCode::UNDEFINED_FORMAT);
            }
        }
    }

    /**
     * Throw error and set code and message
     * @param $code int Error code
     * @param string|bool $message Error message. If FALSE - will be set to default by error code (look to ErrorCode class)
     * @param int|bool $httpCode Http response code. If FALSE will be set to default by error code (look to ErrorCode class)
     *
     * @throws HttpException
     */
    public function throwError($code, $message = false, $httpCode = false)
    {
        if ( ! $this->isError) {
            $this->isError      = true;
            $this->errorCode    = $code;
            $this->errorMessage = $message ? $message : ErrorCode::getMessage($this->errorCode);
            $httpCode = $httpCode?$httpCode:ErrorCode::getHttpCode($this->errorCode);
            throw new HttpException($httpCode, $this->errorMessage);
        }
    }

}