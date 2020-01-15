<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\RiskType;

/**
 * RgsRiskTypeController implements the CRUD actions for RiskType model.
 */
class RgsRiskTypeController extends Controller {

    /**
     * Lists all RiskType models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('RiskType'));
            $post = ['RiskType' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => RiskType::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Imports RiskType models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $riskTypes = $module->riskTypesFromApi;

        foreach ($riskTypes as $riskType) {
            $model = RiskType::find()->where(['ext_id' => $riskType['ID']])->one();

            if (!$model) {
                $model = new RiskType();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model RiskType */
            $model->load([
                'ext_id' => $riskType['ID'],
                'title' => $riskType['Name']
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;
        }

        $deleted = RiskType::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
        ]);
    }

    /**
     * Finds the RiskType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RiskType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = RiskType::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
