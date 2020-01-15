<?php

namespace backend\controllers;

use common\models\GeoTerritory2country;
use Yii;
use common\models\GeoCountry;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GeoCountryController implements the CRUD actions for GeoCountry model.
 */
class GeoCountryController extends Controller
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
     * Lists all GeoCountry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => GeoCountry::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GeoCountry model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new GeoCountry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GeoCountry();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GeoCountry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	GeoTerritory2country::deleteAll(['geo_territory_id' => $model->id]);
        	if ($model->type == GeoCountry::TYPE_TERRITORY){
        		$subCountries = Yii::$app->request->post('subcountry', []);
        		foreach ($subCountries as $subId){
        			$subModel = new GeoTerritory2country([
        				'geo_country_id' => $subId,
				        'geo_territory_id' => $model->id
			        ]);
        			$subModel->save();
		        }
	        }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GeoCountry model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSaveShengen(){
        $checked    = Yii::$app->request->post('checked', 0);
        $country_id = Yii::$app->request->post('country_id', false);

        if ($country_id) {
            $model = GeoCountry::findOne($country_id);
            if ($model) {
                $model->shengen = $checked;
                $model->save();
            }
        }
    }

    public function actionSavePopular(){
        $checked    = Yii::$app->request->post('checked', 0);
        $country_id = Yii::$app->request->post('country_id', false);

        if ($country_id) {
            $model = GeoCountry::findOne($country_id);
            if ($model) {
                $model->is_popular = $checked;
                $model->save();
            }
        }
    }

    /**
     * Finds the GeoCountry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GeoCountry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GeoCountry::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
