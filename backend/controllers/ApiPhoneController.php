<?php

namespace backend\controllers;

use Yii;
use common\models\ApiPhone;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ApiPhoneController implements the CRUD actions for ApiPhone model.
 */
class ApiPhoneController extends Controller
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
     * Lists all ApiPhone models.
     * @return mixed
     */
    public function actionIndex($api_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ApiPhone::find()->where(['api_id' => $api_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'api_id' => $api_id,
        ]);
    }

    /**
     * Creates a new ApiPhone model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($api_id)
    {
        $model = new ApiPhone();
	    $model->api_id = $api_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'api_id' => $model->api_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ApiPhone model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'api_id' => $model->api_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ApiPhone model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
	    $api_id = $model->api_id;
        $model->delete();

        return $this->redirect(['index', 'api_id' => $api_id]);
    }

    /**
     * Finds the ApiPhone model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ApiPhone the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApiPhone::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
