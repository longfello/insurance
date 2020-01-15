<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiLiberty\models\Territory;
use common\modules\ApiLiberty\models\Territory2Product;
use common\modules\ApiLiberty\models\Territory2Dict;
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
class LibertyTerritoryController extends Controller
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
            'query' => Territory::find()->indexBy('id_area'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Territory models.
     * @return mixed
     */
    public function actionUpdate($id)
    {
	    $model = Territory::findOne($id);

	    if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Territory2Dict::deleteAll(['id_area' => $id]);
	    	$data = Yii::$app->request->post('GeoCountry');
	    	foreach($data as $oneId){
	    		if ($oneId) {
				    $geoModel = new Territory2Dict();
				    $geoModel->id_area = $id;
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
        $module  = Yii::$app->getModule('ApiLiberty');
        $products = $module->getProducts();

        foreach($products as $product) {
            $territories = $module->getTerritories($product['productId']);
            Territory2Product::deleteAll(['=', 'productId', $product['productId']]);
            foreach ($territories as $territory) {
                /** @var $model Country */
                $model = Territory::find()->where(['id_area' => $territory['id_area']])->one();
                if (!$model) {
                    $model = New Territory();
                    $inserted++;
                } else {
                    $updated++;
                }

                $model->load((array)$territory, '');
                if (!$model->save()) {
                    var_dump($model->errors);
                    die();
                }
                $ids[] = $territory['id_area'];

                $t2p = new Territory2Product();
                $t2p->id_area = $territory['id_area'];
                $t2p->productId = $product['productId'];
                if (!$t2p->save()) {
                    var_dump($t2p->errors);
                    die();
                }
            }
        }

	    $deleted = Territory::updateAll(['enabled' => 0], ['NOT IN', 'id_area', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);
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
}
