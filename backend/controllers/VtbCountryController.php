<?php

namespace backend\controllers;

use common\models\GeoCountry;
use common\modules\ApiVtb\models\Country2dict;
use common\modules\ApiVtb\models\Region2Country;
use Yii;
use common\modules\ApiVtb\models\Country;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VtbCountryController implements the CRUD actions for Country model.
 */
class VtbCountryController extends Controller
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
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex()
    {
	    if (Yii::$app->request->post('hasEditable')) {
		    // instantiate your book model for saving
		    $id = Yii::$app->request->post('editableKey');
		    $model = Country::findOne($id);

		    // store a default json response as desired by editable
		    $out = Json::encode(['output'=>'', 'message'=>'']);
		    $posted = current($_POST['Country']);
		    $post = ['Country' => $posted];
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
            'query' => Country::find(),
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
			$model = \common\modules\ApiVtb\models\Region2Country::find()->where(['region_id' => $region_id, 'country_id' => $country_id])->one();
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


	public function actionImport()
	{
		$updated = $inserted = $deleted = 0;
		$ids = [];

		$module  = Yii::$app->getModule('ApiVtb');
		/** @var $module \common\modules\ApiVtb\Module */

		$xml = '<?xml version="1.0" encoding="UTF-8" ?>
<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="RequestGetDict.xsd">
		<Dictionaries ProductID ="voyage">
		<Dictionary code="country" />
	</Dictionaries>
</Root>
';
		$res = $module->request('GetDict', $xml);
		$countries = $res['Dictionaries']['Dictionary']['Rows']['Row'];
		foreach($countries as $country){
			$country['name'] = $country['nameRus'];
			$country['currencies'] = $country['currencies']?$country['currencies']:"";
			$country['enabled'] = $country['currencies']?1:0;
			$model = Country::find()->where(['name' => $country['name']])->one();
			if (!$model){
				$model = New Country();
				$inserted++;
			} else {
				$updated++;
			}
			$model->load((array)$country, '');
			$model->shengen = $model->shengen?1:0;
			$model->minInsuranceSum = (int)$model->minInsuranceSum;
			if (!$model->save()) {
				var_dump($model->errors); die();
			}

			if (!$model->countries) {
				$parent = GeoCountry::findOne(['name' => $country['name']]);
				if ($parent){
					$link = new Country2dict();
					$link->api_id = $model->id;
					$link->internal_id = $parent->id;
					$link->save();
				}
			}

			$ids[] = $model->id;
		}

		$deleted = Country::updateAll(['enabled' => 0], ['NOT IN', 'id', $ids]);

		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted,
		]);
	}


    /**
     * Updates an existing Country model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
	public function actionUpdate($id)
	{
		$model = Country::findOne($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Country2dict::deleteAll(['api_id' => $id]);
			$data = Yii::$app->request->post('GeoCountry');
			foreach($data as $oneId){
				if ($oneId) {
					$geoModel = new Country2dict();
					$geoModel->api_id = $id;
					$geoModel->internal_id = $oneId;
					$geoModel->save();
				}
			}
			$this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model,
			'id' => $id,
		]);
	}

    /**
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Country::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
