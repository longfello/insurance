<?php

namespace backend\controllers;

use common\modules\ApiTinkoff\models\Product;
use common\modules\ApiTinkoff\models\Price2Risk;
use common\modules\ApiTinkoff\models\Price2Area;
use common\modules\ApiTinkoff\models\Price2Country;
use common\modules\ApiTinkoff\models\Risk;
use Yii;
use common\modules\ApiTinkoff\models\Price;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TinkoffPriceController implements the CRUD actions for Price model.
 */
class TinkoffPriceController extends Controller
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
     * Creates a new Price model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($product_id)
    {
        $model = new Price();
	    $model->product_id = $product_id;

        $productModel = Product::findOne($product_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->processRisks($model);
            $this->processRegions($model);
            $this->processCountries($model);
            return $this->redirect(['/tinkoff-product/update', 'id' => $model->product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'productModel' => $productModel,
            ]);
        }
    }

    /**
     * Updates an existing Price model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $product_id
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($product_id, $id)
    {
        $model = $this->findModel($id);
	    $model->product_id = $product_id;

        $productModel = Product::findOne($product_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	        $this->processRisks($model);
            $this->processAreas($model);
            $this->processCountries($model);
            return $this->redirect(['/tinkoff-product/update', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'productModel' => $productModel,
            ]);
        }
    }

    /**
     * Updates an existing Price model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $product_id
     * @param integer $id
     * @return mixed
     */
    public function actionClone($product_id, $id)
    {
        $old = $this->findModel($id);
        $new = new Price();
	    $new->attributes = $old->attributes;
	    $new->save();

	    foreach ($old->price2Risks as $risk){
	    	$model = new Price2Risk();
	    	$model->attributes = $risk->attributes;
	    	$model->price_id = $new->id;
	    	$model->save();
	    }

        foreach ($old->price2Areas as $area) {
            $model = new Price2Area();
            $model->attributes = $area->attributes;
            $model->price_id = $new->id;
            $model->save();
        }

        foreach ($old->price2Countries as $country) {
            $model = new Price2Country();
            $model->attributes = $country->attributes;
            $model->price_id = $new->id;
            $model->save();
        }

        return $this->redirect(['/tinkoff-product/update', 'id' => $product_id]);
    }

    /**
     * Deletes an existing Price model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
	    $product_id = $model->product_id;
        $model->delete();

        return $this->redirect(['/tinkoff-product/update', 'id' => $product_id]);
    }

    /**
     * Finds the Price model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Price the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	protected function processRisks(Price $model){
		Price2Risk::deleteAll(['price_id' => $model->id]);
		$risks = Yii::$app->request->post('risk', []);
		foreach ($risks as $risk_id){
			$risk = new Price2Risk();
			$risk->price_id   = $model->id;
			$risk->risk_id    = $risk_id;
			$risk->amount     = Yii::$app->request->post('price_for_'.$risk_id, 0);
			$risk->save();

            $sub_risks = Risk::find()->where(['parent_id'=>$risk_id])->andWhere(['!=','Code','PropertyAddress'])->andWhere(['enabled'=>1])->orderBy( [ 'id' => SORT_ASC ] )->all();

            foreach ($sub_risks as $srisk) {
                $risk = new Price2Risk();
                $risk->price_id   = $model->id;
                $risk->risk_id    = $srisk->id;
                $risk->amount     = Yii::$app->request->post('price_for_'.$srisk->id, 0);
                $risk->save();
            }
		}
	}

    protected function processAreas(Price $model){
        Price2Area::deleteAll(['price_id' => $model->id]);
        $areas = Yii::$app->request->post('area', []);
        foreach ($areas as $area_id){
            $p2a = new Price2Area();
            $p2a->price_id   = $model->id;
            $p2a->area_id    = $area_id;
            $p2a->save();
        }
    }

    protected function processCountries(Price $model){
        Price2Country::deleteAll(['price_id' => $model->id]);
        $countries = Yii::$app->request->post('country', []);
        foreach ($countries as $country_id){
            $p2c = new Price2Country();
            $p2c->price_id    = $model->id;
            $p2c->country_id  = $country_id;
            $p2c->save();
        }
    }
}
