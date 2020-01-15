<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\AdditionalConditionType;
use common\modules\ApiRgs\models\AdditionalConditionTypeRisk;

/**
 * RgsAdditionalConditionTypeController implements the CRUD actions for AdditionalConditionType model.
 */
class RgsAdditionalConditionTypeController extends Controller {

    /**
     * Lists all AdditionalConditionType models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => AdditionalConditionType::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Updates an existing AdditionalConditionType model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (Yii::$app->request->post('Risk')) {
            AdditionalConditionTypeRisk::deleteAll(['additional_condition_type_id' => $id]);

            $data = Yii::$app->request->post('Risk');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $model = new AdditionalConditionTypeRisk();
                    $model->additional_condition_type_id = $id;
                    $model->risk_id = $oneId;
                    $model->save();
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
     * Imports AdditionalConditionType models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $acTypes = $module->additionalConditionTypesFromApi;

        foreach ($acTypes as $acType) {
            $model = AdditionalConditionType::find()->where(['ext_id' => $acType['ID']])->one();

            if (!$model) {
                $model = new AdditionalConditionType();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model AdditionalConditionType */
            $model->load([
                'ext_id' => $acType['ID'],
                'title' => $acType['Name']
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;
        }

        $deleted = AdditionalConditionType::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
        ]);
    }

    /**
     * Finds the AdditionalConditionType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdditionalConditionType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = AdditionalConditionType::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
