<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/4/14
 * Time: 2:01 PM
 */

namespace frontend\controllers;

use Yii;
use common\models\Page;
use common\models\Landing;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class PageController Статические страницы
 * @package frontend\controllers
 */
class PageController extends Controller
{
    /**
     * Просмотр статической страницы
     * @param $slug
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug)
    {
        $model = Page::find()->where(['slug'=>$slug, 'status'=>Page::STATUS_PUBLISHED])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('frontend', 'Page not found'));
        }

        $viewFile = $model->view ?: 'view';
        if ($viewFile=='calc_new') $this->layout = 'new';
        return $this->render($viewFile, ['model'=>$model]);
    }

    /**
     * Просмотр страницы лендинга
     * @param $slug
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionLanding($slug)
    {
        $model = Landing::find()->where(['slug'=>$slug, 'status'=>Page::STATUS_PUBLISHED])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('frontend', 'Page not found'));
        }

        return $this->render('landing', ['model'=>$model]);
    }
}
