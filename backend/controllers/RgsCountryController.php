<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\models\GeoCountry;
use common\modules\ApiRgs\models\TerritoryType;
use common\modules\ApiRgs\models\Country;
use common\modules\ApiRgs\models\Country2dict;

/**
 * RgsCountryController implements the CRUD actions for Country model.
 */
class RgsCountryController extends Controller {

    /**
     * Lists all Country models.
     * @return mixed
     */
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
            'query' => Country::find()->indexBy('id')->orderBy(['title' => SORT_ASC])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Updates an existing Country model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
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

    /**
     * Imports Country models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $territories = TerritoryType::find()->indexBy('ext_id')->all();
        $countries = $module->countriesFromApi;

        foreach ($countries as $country) {
            $model = Country::find()->where(['ext_id' => $country['ID']])->one();

            if (!$model) {
                $model = new Country();
                $inserted++;
            } else {
                $updated++;
            }

            $modelData = [
                'ext_id' => $country['ID'],
                'title' => $country['Description'],
                'enabled' => 1
            ];
            if (isset($territories[$country['ParentItemID']])) {
                $territory = $territories[$country['ParentItemID']];
                $modelData['territory_type_id'] = $territory->id;
            }

            /** @var $model Country */
            $model->load($modelData, '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;

            unset($territory);

            // Set country2dict
            $dict_country = GeoCountry::find()->where(['like', 'name', $model->title])->one();
            if (!empty($dict_country)) {
                $country2dict = Country2dict::find()->where([
                    'internal_id' => $dict_country->id,
                    'country_id' => $model->id
                ])->one();

                if (empty($country2dict)) {
                    $country2dict = new Country2dict();
                    $country2dict->load([
                        'internal_id' => $dict_country->id,
                        'country_id' => $model->id
                    ], '');
                    $country2dict->save();
                }
            }
        }

        $deleted = Country::updateAll(['enabled' => 0], ['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
        ]);
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
