<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\Sum;
use common\modules\ApiRgs\models\Sum2dict;
use common\modules\ApiRgs\models\Program;

/**
 * RgsSumController implements the CRUD actions for Sum model.
 */
class RgsSumController extends Controller {

    /**
     * Lists all Sum models.
     * @return mixed
     */
    public function actionIndex() {
        if (Yii::$app->request->post('hasEditable')) {
            $out = Json::encode(['output' => '', 'message' => '']);
            $id = Yii::$app->request->post('editableKey');
            $model = $this->findModel($id);
            $posted = current(Yii::$app->request->post('Sum'));
            $post = ['Sum' => $posted];

            if ($model->load($post)) {
                $model->save();
            }

            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Sum::find()->indexBy('id')->orderBy([
                'program_id' => SORT_ASC,
                'sum' => SORT_ASC
            ])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new Sum model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Sum();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $id = $model->id;
            $data = Yii::$app->request->post('CostInterval');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $model = new Sum2dict();
                    $model->internal_id = $oneId;
                    $model->sum_id = $id;
                    $model->save();
                }
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Sum model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Sum2dict::deleteAll(['sum_id' => $id]);

            $data = Yii::$app->request->post('CostInterval');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $model = new Sum2dict();
                    $model->internal_id = $oneId;
                    $model->sum_id = $id;
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
     * Imports Sum models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = $skipped = 0;
        $skipped_arr = [];
        $ids = [];
        $programs = Program::find()->indexBy('ext_id')->all();
        $sums = $module->sumsFromApi;

        foreach ($sums as $sum) {
            if (!isset($programs[$sum['ParentItemID']])) {
                $skipped++;
                $skipped_arr[] = $sum;
                continue;
            }

            $program = $programs[$sum['ParentItemID']];

            $model = Sum::find()->where(['ext_id' => $sum['ID']])->one();
            if (!$model) {
                $model = new Sum();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model Sum */
            $model->load([
                'ext_id' => $sum['ID'],
                'title' => $sum['Name'],
                'sum' => $sum['Value1'],
                'program_id' => $program->id,
                'enabled' => 1
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;

            unset($program);
        }

        $deleted = Sum::updateAll(['enabled' => 0], ['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
            'skipped' => $skipped,
            'skipped_arr' => $skipped_arr
        ]);
    }

    /**
     * Finds the Sum model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sum the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = Sum::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
