<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;

use common\modules\ApiZetta\models\ProgramRiskSum;
use common\modules\ApiZetta\models\ProgramRiskSumSearch;

class ZettaRiskSumController extends Controller {

    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);

            $sum = Yii::$app->request->post('Sum');
            foreach ($sum as $k => $v) {
                list($program_id, $risk_id, $sum_id) = explode('-', $k);

                $model = $this->findModel($program_id, $risk_id, $sum_id);
                $model->sum = $v;
                $model->save();
            }

            echo $out;
            return;
        }

        $searchModel = new ProgramRiskSumSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Finds the ProgramRiskSum model.
     * If the model is not found, creates new model.
     * @param integer $program_id
     * @param integer $risk_id
     * @param integer $sum_id
     * @return ProgramRiskSum the loaded model
     */
    protected function findModel($program_id, $risk_id, $sum_id) {
        $model = ProgramRiskSum::findOne([
            'program_id' => $program_id,
            'risk_id' => $risk_id,
            'sum_id' => $sum_id
        ]);

        if ($model === null) {
            $model = new ProgramRiskSum([
                'program_id' => $program_id,
                'risk_id' => $risk_id,
                'sum_id' => $sum_id
            ]);
        }

        return $model;
    }

}
