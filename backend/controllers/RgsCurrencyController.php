<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\models\Currency as DictCurrency;
use common\modules\ApiRgs\models\Currency;
use common\modules\ApiRgs\models\Currency2dict;

/**
 * RgsCurrencyController implements the CRUD actions for Currency model.
 */
class RgsCurrencyController extends Controller {

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
     * Imports Currency models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $currencies = $module->currenciesFromApi;

        foreach ($currencies as $currency) {
            $model = Currency::find()->where(['ext_id' => $currency['ID']])->one();

            if (!$model) {
                $model = new Currency();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model Currency */
            $model->load([
                'ext_id' => $currency['ID'],
                'title' => $currency['CODE'],
                'enabled' => 1
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;

            // Set currency2dict
            $dict_currency = DictCurrency::find()->where(['char_code' => $model->title])->one();
            if (!empty($dict_currency)) {
                $currency2dict = Currency2dict::find()->where([
                    'internal_id' => $dict_currency->id,
                    'currency_id' => $model->id
                ])->one();

                if (empty($currency2dict)) {
                    $currency2dict = new Currency2dict();
                    $currency2dict->load([
                        'internal_id' => $dict_currency->id,
                        'currency_id' => $model->id
                    ], '');
                    $currency2dict->save();
                }
            }
        }

        $deleted = Currency::updateAll(['enabled' => 0], ['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
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
