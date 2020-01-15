<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\Program;
use common\modules\ApiRgs\models\RiskType;
use common\modules\ApiRgs\models\ProgramRisk;

/**
 * RgsProgramController implements the CRUD actions for Program model.
 */
class RgsProgramController extends Controller {

    /**
     * Lists all Program models.
     * @return mixed
     */
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
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ProgramRisk::deleteAll(['program_id' => $id]);

            $data = Yii::$app->request->post('Risk');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $model = new ProgramRisk();
                    $model->program_id = $id;
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
     * Imports Program models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $riskTypes = RiskType::find()->indexBy('ext_id')->all();
        $programs = ArrayHelper::index($module->programsFromApi, 'Name');
        ksort($programs);

        foreach ($programs as $program) {
            $model = Program::find()->where(['ext_id' => $program['ID']])->one();

            if (!$model) {
                $model = new Program();
                $inserted++;
            } else {
                $updated++;
            }

            $modelData = [
                'ext_id' => $program['ID'],
                'title' => !empty($program['Description']) ? $program['Description'] : $program['Name']
            ];
            if (isset($riskTypes[$program['ParentItemID']])) {
                $riskType = $riskTypes[$program['ParentItemID']];
                $modelData['risk_type_id'] = $riskType->id;
            }

            /** @var $model Program */
            $model->load($modelData, '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;

            unset($riskType);
        }

        $deleted = Program::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
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
