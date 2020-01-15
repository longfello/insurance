<?php

namespace backend\controllers;

use common\modules\ApiTinkoff\models\Product;
use Yii;
use common\modules\ApiTinkoff\models\Risk;
use common\modules\ApiTinkoff\models\Risk2Product;
use common\modules\ApiTinkoff\models\Risk2Internal;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RiskController implements the CRUD actions for Risk model.
 */
class TinkoffRiskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Risk models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->post('hasEditable')) {
            // instantiate your book model for saving
            $id = Yii::$app->request->post('editableKey');
            $model = Risk::findOne($id);

            // store a default json response as desired by editable
            $out = Json::encode(['output'=>'', 'message'=>'']);
            $posted = current($_POST['Risk']);
            $post = ['Risk' => $posted];
            if ($model->load($post)) {
                // can save model or do something before saving model
                $model->save();
                $output = '';
                $out = Json::encode(['output'=>$output, 'message'=>'']);
            }
            // return ajax json encoded response and exit
            echo $out;
            return;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Risk::find()->where(['parent_id'=>0])->indexBy('id')->orderBy('id'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $data = Yii::$app->request->post('Risk2internal');
        if (isset($data)) {
            Risk2Internal::deleteAll(['risk_id' => $id]);
            foreach($data as $oneId){
                if ($oneId) {
                    $risk2iModel = new Risk2Internal();
                    $risk2iModel->risk_id = $id;
                    $risk2iModel->internal_id = $oneId;
                    $risk2iModel->save();
                }
            }
            $this->redirect(['index']);
        }

        $childRisksDataProvider = new ActiveDataProvider([
            'query' => Risk::find()->where(['parent_id' => $id]),
        ]);

        return $this->render('view', [
            'childRisksDataProvider' => $childRisksDataProvider,
            'risk_model' => $model
        ]);
    }

    /**
     * Finds the Risk model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Risk the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Risk::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
