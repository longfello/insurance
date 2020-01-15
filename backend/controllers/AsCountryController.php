<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\Country2dict;
use common\modules\ApiAlphaStrah\models\Regions;
use kartik\grid\EditableColumnAction;
use Yii;
use common\modules\ApiAlphaStrah\models\Country;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CountryController implements the CRUD actions for Country model.
 */
class AsCountryController extends Controller
{
	public function actions()
	{
		return ArrayHelper::merge(parent::actions(), [
			'editbook' => [                                       // identifier for your editable column action
				'class' => EditableColumnAction::className(),     // action class name
				'modelClass' => Country::className(),                // the model for the record being edited
				'showModelErrors' => true,                        // show model validation errors after save
				'errorOptions' => ['header' => '']                // error summary HTML options
			]
		]);
	}

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

	public function actionSaveVisa(){
		$checked    = Yii::$app->request->post('checked', 0);
		$country_id = Yii::$app->request->post('country_id', false);

		if ($country_id) {
			$model = Country::findOne(['countryID'=>$country_id]);
			if ($model) {
				$model->visa = $checked;
				$model->save();
			}
		}
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
            'query' => Country::find()->indexBy('countryID'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Country models.
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

    public function actionImport()
    {
    	$updated = $inserted = $deleted = 0;
	    $ids = [];
	    $countries = Yii::$app->getModule('ApiAlphaStrah')->getCountries();
	    foreach($countries as $country){
	    	$model = Country::find()->where(['countryID' => $country->countryID])->one();
	    	if (!$model){
	    		$model = New Country();
	    		$inserted++;
		    } else {
	    		$updated++;
		    }

		    /** @var $model Country */
			$model->load((array)$country, '');
			$model->enabled = 1;
			$region = Regions::findOne(['short_name'=>$country->terName]);
			$model->region_id = ($region)?$region->id: new \yii\db\Expression('NULL');
 		    if (!$model->save()) {
		    	var_dump($model->errors); die();
		    }
		    $ids[] = $model->countryID;
	    }

	    $deleted = Country::updateAll(['enabled' => 0], ['NOT IN', 'countryID', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);
    }

    /**
     * Displays a single Country model.
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
