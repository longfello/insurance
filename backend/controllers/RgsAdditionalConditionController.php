<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\AdditionalCondition;
use common\modules\ApiRgs\models\AdditionalConditionType;

/**
 * RgsAdditionalConditionController implements the CRUD actions for AdditionalCondition model.
 */
class RgsAdditionalConditionController extends Controller {

    /**
     * Lists all AdditionalCondition models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('AdditionalCondition'));
            $post = ['AdditionalCondition' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => AdditionalCondition::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Imports AdditionalCondition models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = $skipped = 0;
        $skipped_arr = [];
        $ids = [];
        $acTypes = AdditionalConditionType::find()->indexBy('ext_id')->all();
        $additionalConditions = ArrayHelper::index($module->additionalConditionsFromApi, null, 'ParentItemID');

        foreach ($additionalConditions as $acTypeId => $additionalCondition) {
            if (!isset($acTypes[$acTypeId])) {
                $skipped++;
                $skipped_arr[$acTypeId] = $additionalCondition;
                continue;
            }

            $acType = $acTypes[$acTypeId];
            foreach ($additionalCondition as $item) {
                $model = AdditionalCondition::find()->where(['ext_id' => $item['ID']])->one();
                if (!$model) {
                    $model = new AdditionalCondition();
                    $inserted++;
                } else {
                    $updated++;
                }

                /** @var $model AdditionalCondition */
                $model->load([
                    'ext_id' => $item['ID'],
                    'title' => $item['Name'],
                    'additional_condition_type_id' => $acType->id
                ], '');

                if (!$model->save()) {
                    var_dump($model->errors);
                    exit;
                }

                $ids[] = $model->id;
            }
            unset($acType);
        }

        $deleted = AdditionalCondition::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
            'skipped' => $skipped,
            'skipped_arr' => $skipped_arr
        ]);
    }

    /**
     * Finds the AdditionalCondition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdditionalCondition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = AdditionalCondition::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
