<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiLiberty\models\Product;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class LibertyProductController extends Controller
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render( 'update', [
                'model' => $model
            ] );
        }
    }

    public function actionImport()
    {
        $updated = $inserted = $deleted = 0;
        $ids = [];

        $module  = Yii::$app->getModule('ApiLiberty');
        $products = $module->getProducts();

        foreach($products as $product){
            /** @var $model Product */
            $model = Product::find()->where(['productId' => $product['productId']])->one();

            if (!$model){
                $model = New Product();
                $inserted++;
            } else {
                $updated++;
            }

            $model->load((array)$product, '');
            if (!$model->save()) {
                var_dump($model->errors); die();
            }

            $ids[] = $model->productId;
        }

        $deleted = Product::deleteAll(['NOT IN', 'productId', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);

    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
