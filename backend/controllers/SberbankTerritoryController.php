<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiSberbank\models\Territory;
use common\modules\ApiSberbank\models\Territory2Dict;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TerritoryController implements the CRUD actions for Territory model.
 */
class SberbankTerritoryController extends Controller
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
     * Lists all Territory models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->post('hasEditable')) {
            // instantiate your book model for saving
            $id = Yii::$app->request->post('editableKey');
            $model = Territory::findOne($id);

            // store a default json response as desired by editable
            $out = Json::encode(['output'=>'', 'message'=>'']);
            $posted = current($_POST['Territory']);
            $post = ['Territory' => $posted];
            if ($model->load($post)) {
                // can save model or do something before saving model
                $model->save();
                $output = '';
                $out = Json::encode(['output'=>$output, 'message'=>'']);
            }
            // return ajax json encoded response and exit
            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Territory::find()->indexBy('id'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Territory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Territory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->processDict($model);
            return $this->redirect(['index']);
        } else {
            $model->enabled = 1;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists all Territory models.
     * @return mixed
     */
    public function actionUpdate($id)
    {
	    $model = Territory::findOne($id);

	    if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->processDict($model);
	    	$this->redirect(['index']);
	    }

        return $this->render('update', [
            'model' => $model,
            'id' => $id,
        ]);
    }

    /**
     * Deletes an existing Territory model.
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
     * Finds the Territory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Territory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Territory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function processDict(Territory $territory) {
        Territory2Dict::deleteAll(['territory_id' => $territory->id]);
        $data = Yii::$app->request->post('GeoCountry');
        foreach($data as $oneId){
            if ($oneId) {
                $geoModel = new Territory2Dict();
                $geoModel->territory_id = $territory->id;
                $geoModel->internal_id = $oneId;
                $geoModel->save();
            }
        }
    }
}
