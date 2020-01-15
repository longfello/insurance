<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\Price2risk;
use common\modules\ApiAlphaStrah\models\PriceInc;
use Yii;
use common\modules\ApiAlphaStrah\models\Price;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AsPriceController implements the CRUD actions for Price model.
 */
class AsPriceController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Price model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($program_id)
    {
        $model = new Price();
	    $model->program_id = $program_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'program_id' => $program_id, 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Price model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $program_id
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($program_id, $id)
    {
        $model = $this->findModel($id);
	    $model->program_id = $program_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	        $this->processRisks($model);
            return $this->redirect(['/as-insurance-programm/update', 'id' => $model->program_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Price model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Price model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Price the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	protected function processRisks(Price $model){
		PriceInc::deleteAll(['price_id' => $model->id]);

		$names   = Yii::$app->request->post('price_inc_name', []);
		$amounts = Yii::$app->request->post('price_inc_amount', []);
		$filter_ids = Yii::$app->request->post('price_inc_filter_id', []);

		foreach ($names as $key => $name){
			$inc = new PriceInc();
			$inc->price_id   = $model->id;
			$inc->name       = $name;
			$inc->amount     = $amounts[$key];
			$inc->filter_id = $filter_ids[$key];
			$inc->save();
		}
	}

}
