<?php

namespace backend\controllers;

use common\modules\ApiErv\models\Region2Country;
use Yii;
use common\models\GeoCountry;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ErvCountryController implements the CRUD actions for GeoCountry model.
 */
class ErvCountryController extends Controller
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

    public function actionSave(){
    	$country_id = Yii::$app->request->post('country_id', false);
    	$region_id  = Yii::$app->request->post('region_id', false);
	    $checked    = Yii::$app->request->post('checked', false);

    	if ($country_id && $region_id) {
    		$model = \common\modules\ApiErv\models\Region2Country::find()->where(['region_id' => $region_id, 'country_id' => $country_id])->one();
    		if ($checked) {
    			if (!$model){
    				$model = new Region2Country();
    				$model->region_id = $region_id;
    				$model->country_id = $country_id;
    				$model->save();
			    }
		    } else {
    			if ($model){
    				$model->delete();
			    }
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
