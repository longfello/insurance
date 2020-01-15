<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace console\controllers;

use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Response;

class RestTestController extends Controller
{
    public $format = \yii\web\Response::FORMAT_JSON;
    public $method = 'post';
    public $file = '';
    public $login = 'admin';
    public $password = 'ghjtrn9';
    public $action;

    public function options($actionID)
    {
        return ['login', 'password', 'file', 'format', 'action', 'method'];
    }

    public function optionAliases()
    {
        return ['u' => 'login', 'p' => 'password', 'f' => 'file'];
    }

    public function prepareFile()
    {
        if (!$this->file) return NULL;
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'test-data' . DIRECTORY_SEPARATOR . $this->method . DIRECTORY_SEPARATOR . $this->format . DIRECTORY_SEPARATOR . $this->file;
        if (!file_exists($file)){
            echo("File not found: {$file} \r\n");
            \Yii::$app->end();
        }
        return file_get_contents($file);
    }

    public function actionIndex($action)
    {
        $url = \Yii::$app->urlManagerApi->createAbsoluteUrl("/{$action}?format={$this->format}");
        echo("Test rest API v.0.1\r\n");
        echo("Trying to authenticate as {$this->login} / {$this->password}\r\n");
        echo("\r\n".strtoupper($this->method)." {$url}\r\n");

        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        $request = $client->createRequest();
        $request->setMethod($this->method);
        $request->setUrl($url);
        $request->addHeaders(['Authorization' => 'Basic ' . base64_encode($this->login.':'.$this->password)]);
        if ($this->file){

        }
        $request->setData(['data' => $this->prepareFile()]);
        $response = $request->send();
        /** @var $response Response */
        echo ("\r\nResponse:");
        echo ("\r\n".str_pad('',80, "=")."\r\n");
        echo ($response->getContent());
        echo ("\r\n".str_pad('',80, "=")."\r\n");
        echo ("\r\n\r\n");
    }
}