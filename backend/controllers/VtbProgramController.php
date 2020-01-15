<?php

namespace backend\controllers;

use common\modules\ApiVtb\models\Price;
use common\modules\ApiVtb\models\Price2risk;
use Yii;
use common\modules\ApiVtb\models\Program;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VtbProgramController implements the CRUD actions for Program model.
 */
class VtbProgramController extends Controller
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
     * Lists all Program models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Program::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Program();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {

	        $dataProvider = new ActiveDataProvider([
		        'query' => Price::find()->where(['program_id' => $model->id])->orderBy(['region_id' => SORT_ASC, 'amount_id' => SORT_ASC, 'period_id' => SORT_ASC]),
	        ]);

            return $this->render('update', [
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionClone($id)
    {
	    $oldModel = $this->findModel($id);
	    $newModel = new Program();
	    $newModel->attributes = $oldModel->attributes;
	    $newModel->save();
        foreach($oldModel->prices as $old){
	        $new = new Price();
	        $new->attributes = $old->attributes;
	        $new->program_id = $newModel->id;
	        $new->save();

	        foreach ($old->apiVtbPrice2risks as $risk){
		        $model = new Price2risk();
		        $model->attributes = $risk->attributes;
		        $model->price_id = $new->id;
		        $model->save();
	        }
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Program model.
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
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
