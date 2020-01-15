<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace api\controllers;

use api\components\Controller;
use common\models\Page;
use Intervention\Image\Exception\NotFoundException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ApiController Контроллер АПИ
 * @package api\controllers
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * Страница документации
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionIndex()
	{
	    \Yii::$app->response->format = \api\components\Response::FORMAT_HTML;
        return $this->render('index');
	}
    /**
     * Run REST API method
     * @return mixed
     */
    public function actionProceed(){
        return \Yii::$app->rest->process();
    }

    /**
     * Render HTTP error exception
     * @return string
     */
    public function actionError(){
        $exception = \Yii::$app->errorHandler->exception;
        return $this->render('error', ['exception' => $exception]);
    }
}
