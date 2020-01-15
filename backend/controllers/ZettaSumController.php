<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use common\modules\ApiZetta\models\Sum;
use common\modules\ApiZetta\models\Sum2dict;
use common\modules\ApiZetta\models\Currency;

class ZettaSumController extends Controller {

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
            'query' => Sum::find()->indexBy('id')
//            'query' => Sum::find()->alias('azs')->innerJoin(Currency::tableName() . ' azc', 'azs.currency_id=azc.id')->where(['azc.enabled' => 1])->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Lists all Risk models.
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Sum2dict::deleteAll(['sum_id' => $id]);

            $data = Yii::$app->request->post('CostInterval');
            foreach ($data as $oneId) {
                if ($oneId) {
                    $riskModel = new Sum2dict();
                    $riskModel->internal_id = $oneId;
                    $riskModel->sum_id = $id;
                    $riskModel->save();
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
