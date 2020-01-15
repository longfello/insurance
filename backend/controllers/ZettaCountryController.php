<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\modules\ApiZetta\models\Country;
use common\modules\ApiZetta\models\Currency;
use common\modules\ApiZetta\models\CountryCurrency;
use common\modules\ApiZetta\models\CountryTerritory;
use common\modules\ApiZetta\models\Country2dict;

/**
 * ZettaCountryController implements the CRUD actions for GeoCountry model.
 */
class ZettaCountryController extends Controller {

    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('Country'));
            $post = ['Country' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Country::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Country model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Country2dict::deleteAll(['country_id' => $id]);

            $data = Yii::$app->request->post('GeoCountry');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $geoModel = new Country2dict();
                    $geoModel->internal_id = $oneId;
                    $geoModel->country_id = $id;
                    $geoModel->save();
                }
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'id' => $id
        ]);
    }

    public function actionImport() {
        $module = Yii::$app->getModule('ApiZetta');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $countries = $module->countriesFromApi;

        foreach ($countries as $country) {
            $model = Country::find()->where(['ext_id' => $country['ID']])->one();

            if (!$model) {
                $model = new Country();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model Country */
            $model->load([
                'ext_id' => $country['ID'],
                'title' => $country['Name'],
                'enabled' => 1
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;
        }

        $deleted = Country::updateAll(['enabled' => 0], ['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
        ]);
    }

    public function actionUpdateCurrencies() {
        $module = Yii::$app->getModule('ApiZetta');
        $countries = Country::find()->all();

        foreach ($countries as $country) {
            CountryCurrency::deleteAll(['country_id' => $country->id]);

            $currencies = $module->getCountryCurrenciesFromApi($country);
            if ($currencies === null) {
                continue;
            }
            foreach ($currencies as $data) {
                $currency = Currency::find()->where(['ext_id' => trim($data['ID'])])->one();
                if (!empty($currency)) {
                    $model = new CountryCurrency([
                        'country_id' => $country->id,
                        'currency_id' => $currency->id
                    ]);
                    $model->save();
                }
            }
        }

        return $this->redirect(['index']);
    }

    public function actionUpdateTerritories() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiZetta');
        $countries = Country::find()->indexBy('ext_id')->all();
        $countryTerritories = ArrayHelper::index($module->countryTerritoryFromApi, null, ['Country']);

        foreach ($countryTerritories as $country_ext_id => $territories) {
            $country = $countries[strtolower($country_ext_id)];
            if (empty($country->type)) {
                $country->type = \common\models\GeoCountry::TYPE_COUNTRY;
                $country->save();
            }
            CountryTerritory::deleteAll(['country_id' => $country->id]);

            foreach ($territories as $data) {
                $territory = $countries[strtolower($data['Territory'])];
                if (empty($territory->type)) {
                    $territory->type = \common\models\GeoCountry::TYPE_TERRITORY;
                    $territory->save();
                }

                $model = new CountryTerritory([
                    'country_id' => $country->id,
                    'territory_id' => $territory->id
                ]);
                $model->save();
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = Country::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
