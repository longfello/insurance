<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\controllers;

use common\components\ApiModule;
use common\models\Api;
use common\models\Orders;
use common\models\Page;
use common\models\ProgramResult;
use common\models\User;
use common\modules\ApiErv\models\Program;
use common\modules\ApiErv\Module;
use common\components\Calculator\filters\Filter;
use common\components\Calculator\forms\TravelForm;
use frontend\models\PersonInfo;
use Yii;
use frontend\models\ContactForm;
use yii\bootstrap\Html;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Search controller - Поиск
 */
class SearchController extends Controller
{

    /**
     * Поиск по статическим страницам
     * @return string
     */
    public function actionIndex()
    {
    	$q = Yii::$app->request->post('q', false);
	    $models = []; $error = false;
    	if ($q) {
		    $models = Page::find()->filterWhere(['like', 'body', $q])->all();
	    }
        return $this->render('index', [
        	'models' => $models,
	        'error'  => $error,
	        'q'      => $q
        ]);
    }

}
