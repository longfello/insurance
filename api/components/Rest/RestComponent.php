<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 17.10.17
 * Time: 10:40
 */

namespace api\components\Rest;

use api\components\ErrorCode;
use common\models\Orders;
use common\models\User;
use yii\base\Component;
use yii\helpers\BaseFileHelper;
use yii\helpers\BaseInflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\HttpException;
use \Yii;

/**
 * Class Rest - base rest api component
 * @package portal\components
 */
class RestComponent extends Component
{
    /** Не авторизирован */
    const AUTH_NONE = false;
    /** Авторизирован через Base Auth */
    const AUTH_BASIC = 'basic';
    /** Авторизирован через параметр calc_token */
    const AUTH_CALC_TOKEN  = 'calc_token';

    /** @var User|null Current partner */
    public $user = null;
    /** @var boolean Is debug mode switched ON */
    public $debug = false;

    public $version = '0.2';
    /**
     * Request handler
     * @var RestMethod|null
     */
    public $handler;

    /** Метод аутентификации запроса */
    public $authedBy = self::AUTH_NONE;
    /**
     * Данные аутентификации
     * @var null|string|Orders
     */
    public $token;


    /**
     * Process API method
     * @throws \Exception
     */
    public function process(){
        $this->authenticate();

        $this->debug = $this->debug || ($this->user && $this->user->can('administrator'));

        $method = BaseInflector::camelize(strtolower(\Yii::$app->request->pathInfo));
        $className = "\api\components\Rest\\".BaseInflector::camelize(strtolower(Yii::$app->request->method))."\\".BaseInflector::camelize($method);
        if (class_exists($className) && is_subclass_of($className, RestMethod::className())){
            $this->handler = new $className();
            /** @var $handler RestMethod */
            $response = $this->handler->run();
            $this->handler->log->response = json_encode($response);
            $this->handler->log->save();
            return $response;
        } else {
            Yii::$app->response->throwError(ErrorCode::NOT_FOUND, "Метод не найден: ". Yii::$app->request->method .' '.str_replace('_', '/', BaseInflector::underscore($method)));
        }
    }

    public function compileDocumentation(){
        // Документация по методам
        $methods = $this->getMethods();
        foreach ($methods as $method){
            /** @var $method RestMethod */

            if (!$method->shareDocumentation) continue;

            $rc = new \ReflectionClass($method);

            $docComment = $rc->getDocComment();

            if ($docComment) {
                $date = date('d.m.Y', filemtime(__FILE__));
                $docComment = str_replace('{%api_version}', $this->version, $docComment);
                $docComment = str_replace('{%api_date}', $date, $docComment);
                $docComment = str_replace('{%api_url}', Url::to('/', true), $docComment);

                $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                $docblock = $factory->create($docComment);

                $comment = $docblock->getDescription()->render();

                $parser = new \cebe\markdown\GithubMarkdown();
                $parser->html5 = true;

                $comment = $parser->parse($comment);

                $id = BaseInflector::underscore(StringHelper::basename($method::className()));
                echo("<br><a id='{$id}'></a>$comment");
            }
        }

        // Документация по кодам ошибок
        $rc = new \ReflectionClass(ErrorCode::className());

        $docComment = $rc->getDocComment();

        if ($docComment) {
            $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
            $docblock = $factory->create($docComment);

            $comment = $docblock->getDescription()->render();

            $parser = new \cebe\markdown\GithubMarkdown();
            $parser->html5 = true;

            $comment = $parser->parse($comment);

            echo("$comment");
        }

        $constants = $rc->getConstants();
        $head = " Код ошибки | HTTP-код ответа | Комментарий \r\n :---: | :---: | --- \r\n";
        $line = " {%1} | {%2} | {%3} \r\n";
        foreach ($constants as $constant){
            $newline = $line;
            $newline = str_replace('{%1}', $constant, $newline);
            $newline = str_replace('{%2}', ErrorCode::getHttpCode($constant), $newline);
            $newline = str_replace('{%3}', ErrorCode::getMessage($constant), $newline);
            $head .= $newline;
        }
        $parser = new \cebe\markdown\GithubMarkdown();
        $parser->html5 = true;

        $comment = $parser->parse($head);

        echo("$comment<br>");
    }

    /**
     *
     */
    private function getMethods(){
        $methods = [];
        $dir = Yii::getAlias('@api/components/Rest');
        $files = BaseFileHelper::findFiles($dir);
        foreach ($files as $file){
            $className = str_replace([$dir, '.php'], '', $file);
            $className = '\api\components\Rest'.str_replace('/', '\\', $className);

            if (class_exists($className) && is_a($className, RestMethod::className(), true)){
                $methods[] = new $className();
            }
        }

        usort($methods, function($a, $b){
            if ($a->sort_order == $b->sort_order) {
                /** @var $a RestMethod */
                /** @var $b RestMethod */
                return ($a->className() < $b->className())? -1 : 1;
            }
            return ($a->sort_order < $b->sort_order) ? -1 : 1;
        });

        return $methods;
    }

    /**
     * Authentificate partner. By default - via Http Base Auth
     * @return bool Is auth successful
     * @throws HttpException Fired on authentification denied
     */
    private function authenticate(){
        // Попытка авторизовать через BASIC AUTH
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            //check user/pass
            if ($user = User::findByLogin($_SERVER['PHP_AUTH_USER'])){
                if ($user->validatePassword($_SERVER['PHP_AUTH_PW'])){
                    if ($user->status == User::STATUS_ACTIVE) {
                        $this->user = $user;
                        $this->authedBy = self::AUTH_BASIC;
                        return true;
                    }
                }
            }
        }

        // Попытка авторизовать через Calc token
        if ($token = Yii::$app->request->get('token', null)){
            if ($this->token = Orders::findOne(['slug' => $token])){
                $this->authedBy = self::AUTH_CALC_TOKEN;
                return true;
            }
        }

        Yii::$app->response->throwError(ErrorCode::AUTH_DENIED);
    }
}