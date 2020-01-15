<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\controllers;

use common\models\ProgramResult;
use common\components\Calculator\forms\TravelForm;
use Yii;
use frontend\models\ContactForm;
use yii\base\Model;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Class ValidateController - валидация
 * @package frontend\controllers
 */
class ValidateController extends Controller
{
    /**
     * Валидация формы
     * @return array
     * @throws HttpException
     */
    public function actionForm()
    {
	    Yii::$app->response->format = Response::FORMAT_JSON;
    	switch (Yii::$app->request->post('form_type')){
		    case \common\components\Calculator\forms\prototype::SLUG_TRAVEL:
		    	if ($programData = Yii::$app->request->post('program', false)){
				    $program = new ProgramResult();
				    $program->loadFromJson($programData, TravelForm::SCENARIO_PREPAY);
				    $model = $program->calc;
			    } else {
				    $model = new TravelForm();
			    }

		    	$model->scenario = Yii::$app->request->post('form_scenario', Model::SCENARIO_DEFAULT);
			    $model->load(\Yii::$app->request->post());
			    if ($model->validate()) {
			    	return $this->allRight();
			    } else {
					return $this->errors($model->getErrors());
			    }
		    	break;
	    }

	    Yii::$app->response->format = Response::FORMAT_HTML;
    	throw new HttpException(404);
    }

    /**
     * Форматированный ответ - всё Ок
     * @return array
     */
    public function allRight(){
	    return ['result' => true];
    }

    /**
     * Форматированный ответ - есть ошибки
     * @param $data
     *
     * @return array
     */
    public function errors($data){
	    return [
	    	'result' => false,
		    'errors' => $data
	    ];
    }
}
