<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use common\modules\ApiZetta\models\Program;
use common\modules\ApiZetta\models\ProgramRisk;

/**
 * InsuranceProgrammController implements the CRUD actions for InsuranceProgramm model.
 */
class ZettaInsuranceProgrammController extends Controller {

    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('Program'));
            $post = ['Program' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Program::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Lists all Program models.
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ProgramRisk::deleteAll(['program_id' => $id]);

            $data = Yii::$app->request->post('Risk');
            if (!empty($data)) {
                foreach ($data as $oneId) {
                    if ($oneId) {
                        $riskModel = new ProgramRisk();
                        $riskModel->risk_id = $oneId;
                        $riskModel->program_id = $id;
                        $riskModel->save();
                    }
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
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = Program::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
