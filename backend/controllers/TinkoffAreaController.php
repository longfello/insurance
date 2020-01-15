<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiTinkoff\models\Area;
use common\modules\ApiTinkoff\models\Area2Product;
use common\modules\ApiTinkoff\models\Area2Dict;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AreaController implements the CRUD actions for Area model.
 */
class TinkoffAreaController extends Controller
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
     * Lists all Area models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->post('hasEditable')) {
            // instantiate your book model for saving
            $id = Yii::$app->request->post('editableKey');
            $model = Area::findOne($id);

            // store a default json response as desired by editable
            $out = Json::encode(['output'=>'', 'message'=>'']);
            $posted = current($_POST['Area']);
            $post = ['Area' => $posted];
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
            'query' => Area::find()->indexBy('id'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Area models.
     * @return mixed
     */
    public function actionUpdate($id)
    {
	    $model = Area::findOne($id);

	    if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Area2Dict::deleteAll(['area_id' => $id]);
	    	$data = Yii::$app->request->post('GeoCountry');
	    	foreach($data as $oneId){
	    		if ($oneId) {
				    $geoModel = new Area2Dict();
				    $geoModel->area_id = $id;
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
     * Finds the Area model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Area the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Area::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
