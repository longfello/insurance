<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use common\modules\ApiZetta\models\Currency;
use common\modules\ApiZetta\models\Currency2dict;

class ZettaCurrencyController extends Controller {

    /**
     * Lists all Currency models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('Currency'));
            $post = ['Currency' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Currency::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Updates an existing Currency model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Currency2dict::deleteAll(['currency_id' => $id]);

            $data = Yii::$app->request->post('CommonCurrency');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $currencyModel = new Currency2dict();
                    $currencyModel->internal_id = $oneId;
                    $currencyModel->currency_id = $id;
                    $currencyModel->save();
                }
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $id
        ]);
    }

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = Currency::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
