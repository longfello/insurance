<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiLiberty\models\Occupation;
use common\modules\ApiLiberty\models\Occupation2Product;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OccupationController implements the CRUD actions for Occupation model.
 */
class LibertyOccupationController extends Controller
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
     * Lists all Occupation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Occupation::find()->indexBy('id'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSaveSport() {
        $occupationId = Yii::$app->request->post('occupationId');
        $checked = Yii::$app->request->post('checked');

        $model = $this->findModel($occupationId);
        $model->is_sport = ($checked==1)?1:0;
        if (!$model->save()) {
            var_dump($model->errors);
            die();
        }
    }


    public function actionImport()
    {
    	$updated = $inserted = $deleted = 0;
	    $ids = [];
        $module  = Yii::$app->getModule('ApiLiberty');
        $products = $module->getProducts();

        foreach($products as $product) {
            $occupations = $module->getOccupations($product['productId']);
            Occupation2Product::deleteAll(['=', 'productId', $product['productId']]);
            foreach ($occupations as $occupation) {
                /** @var $model Occupation */
                $model = Occupation::find()->where(['id' => $occupation['id']])->one();
                if (!$model) {
                    $model = New Occupation();
                    $inserted++;
                } else {
                    $updated++;
                }

                $model->load((array)$occupation, '');
                if (!$model->save()) {
                    var_dump($model->errors);
                    die();
                }
                $ids[] = $occupation['id'];

                $t2p = new Occupation2Product();
                $t2p->id = $occupation['id'];
                $t2p->productId = $product['productId'];
                if (!$t2p->save()) {
                    var_dump($t2p->errors);
                    die();
                }
            }
        }

	    $deleted = Occupation::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);
    }

    /**
     * Finds the Occupation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Occupation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Occupation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
