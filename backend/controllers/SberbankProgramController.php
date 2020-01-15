<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiSberbank\models\Program;
use common\modules\ApiSberbank\models\Program2Risk;
use common\modules\ApiSberbank\models\Territory2Program;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SberbankProgramController implements the CRUD actions for Program model.
 */
class SberbankProgramController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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
	        $this->processRisks($model);
	        $this->processTerritories($model);
	        return $this->redirect(['index']);
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
	        $this->processRisks($model);
	        $this->processTerritories($model);
	        return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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

	protected function processRisks(Program $model){
    	Program2Risk::deleteAll(['program_id' => $model->id]);
    	$risks = Yii::$app->request->post('risk', []);
    	foreach ($risks as $risk_id){
    		$risk = new Program2Risk();
    		$risk->program_id  = $model->id;
    		$risk->risk_id     = $risk_id;
		    $risk->summa       = (int)Yii::$app->request->post('price_for_'.$risk_id, 0);
		    $risk->summa       = $risk->summa?$risk->summa:0;
            $risk->is_optional = (int)Yii::$app->request->post('is_optional_'.$risk_id, 0);
            $risk->name        = Yii::$app->request->post('name_for_'.$risk_id, '');
    		$risk->save();
	    }
	}

    protected function processTerritories(Program $model) {
        Territory2Program::deleteAll(['program_id' => $model->id]);
        $territories = Yii::$app->request->post('territory', []);
        foreach ($territories as $territory_id){
            $t2p = new Territory2Program();
            $t2p->program_id   = $model->id;
            $t2p->territory_id = $territory_id;
            $t2p->save();
        }
    }

}
