<?php

namespace backend\controllers;

use common\modules\ApiVtb\models\Price2risk;
use common\modules\ApiVtb\models\Program;
use common\modules\ApiVtb\models\Risk;
use Yii;
use common\modules\ApiVtb\models\Price;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VtbPriceController implements the CRUD actions for Price model.
 */
class VtbPriceController extends Controller
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
            return $this->redirect(['/vtb-program/update', 'id' => $model->program_id]);
        } else {
            return $this->render('update', [
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
    public function actionClone($program_id, $id)
    {
        $old = $this->findModel($id);
        $new = new Price();
	    $new->attributes = $old->attributes;
	    $new->save();

	    foreach ($old->apiVtbPrice2risks as $risk){
	    	$model = new Price2risk();
	    	$model->attributes = $risk->attributes;
	    	$model->price_id = $new->id;
	    	$model->save();
	    }

        return $this->redirect(['/vtb-program/update', 'id' => $program_id]);
    }

    /**
     * Deletes an existing Price model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
	    $program_id = $model->program_id;
        $model->delete();

        return $this->redirect(['/vtb-program/update', 'id' => $program_id]);
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
		Price2risk::deleteAll(['price_id' => $model->id]);
		$risks = Yii::$app->request->post('risk', []);
		foreach ($risks as $risk_id){
			$risk = new Price2risk();
			$risk->price_id   = $model->id;
			$risk->risk_id    = $risk_id;
			$risk->amount     = Yii::$app->request->post('price_for_'.$risk_id, 0);
			$risk->save();
		}
	}

}
