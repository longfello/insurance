<?php

namespace backend\controllers;

use common\components\Calculator\models\travel\FilterSolution2param;
use Yii;
use common\components\Calculator\models\travel\FilterSolution;
use common\components\Calculator\models\travel\FilterSolution2country;
use common\components\Calculator\models\travel\FilterSolution2api;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FilterSolutionController implements the CRUD actions for FilterSolution model.
 */
class FilterSolutionController extends Controller
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
     * Lists all FilterSolution models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FilterSolution::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new FilterSolution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FilterSolution();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveCountries($model->id);
            $this->saveApi($model->id);
            $this->saveParams($model->id);
	        return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing FilterSolution model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveCountries($model->id);
            $this->saveApi($model->id);
            $this->saveParams($model->id);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'params' =>  $this->loadParams($model->id)
            ]);
        }
    }

    /**
     * Deletes an existing FilterSolution model.
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
     * Finds the FilterSolution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FilterSolution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FilterSolution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function saveCountries($id) {
        FilterSolution2country::deleteAll(['filter_solution_id' => $id]);
        $data = Yii::$app->request->post('GeoCountry',[]);
        foreach($data as $oneId){
            if ($oneId) {
                $geoModel = new FilterSolution2country();
                $geoModel->filter_solution_id = $id;
                $geoModel->country_id = $oneId;
                $geoModel->save();
            }
        }
    }

    private function saveApi($id) {
        FilterSolution2api::deleteAll(['filter_solution_id' => $id]);
        $data = Yii::$app->request->post('Api',[]);
        foreach($data as $oneId){
            if ($oneId) {
                $apiModel = new FilterSolution2api();
                $apiModel->filter_solution_id = $id;
                $apiModel->api_id = $oneId;
                $apiModel->save();
            }
        }
    }

    private function saveParams($id) {
        FilterSolution2param::deleteAll(['filter_solution_id' => $id]);
        $filters = \common\components\Calculator\models\travel\FilterParam::find()->all();
        foreach($filters as $filter){
            /** @var $filter \common\components\Calculator\models\travel\FilterParam */
            if ($filter && $filter->handler) {
                $filter->handler->load();
                if ($filter->handler->checked) {
                    $value = $filter->handler->getVariantValue();

                    $paramModel = new FilterSolution2param();
                    $paramModel->filter_solution_id = $id;
                    $paramModel->param_id = $filter->id;
                    $paramModel->value = ($value)?$value:'1';
                    $paramModel->save();
                }
            }
        }
    }

    private function loadParams($id) {
        $result = [];
        $params = FilterSolution2param::findAll(['filter_solution_id' => $id]);
        foreach ($params as $one) {
            $result[$one->param_id] = $one->value;
        }
        return $result;
    }
}
