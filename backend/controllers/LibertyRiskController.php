<?php

namespace backend\controllers;

use common\modules\ApiLiberty\models\Product;
use Yii;
use common\modules\ApiLiberty\models\Risk;
use common\modules\ApiLiberty\models\Risk2Product;
use common\modules\ApiLiberty\models\Risk2Internal;
use common\modules\ApiLiberty\models\Summ;
use common\modules\ApiLiberty\models\Summ2Interval;
use common\modules\ApiLiberty\models\Summ2Cost;
use common\modules\ApiLiberty\models\Territory;
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
class LibertyRiskController extends Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => Risk::find()->indexBy('riskId')->orderBy('riskId'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Описание успешно сохранено');
            $this->redirect(['view', 'id'=>$id]);
        } else {
            $data = Yii::$app->request->post('Risk2internal');
            if (isset($data)) {
                Risk2Internal::deleteAll(['riskId' => $id]);
                foreach ($data as $oneId) {
                    if ($oneId) {
                        $risk2iModel = new Risk2Internal();
                        $risk2iModel->riskId = $id;
                        $risk2iModel->internal_id = $oneId;
                        $risk2iModel->save();
                    }
                }
                $this->redirect(['index']);
            }

            $summDataProvider = new ActiveDataProvider([
                'query' => Summ::find()->where(['riskId' => $id])->groupBy(['productId', 'amount']),
            ]);

            return $this->render('view', [
                'risk_model' => $model,
                'summDataProvider' => $summDataProvider,
            ]);
        }
    }

    public function actionSaveCost() {
        $riskId = Yii::$app->request->post('riskId');
        $productId = Yii::$app->request->post('productId');
        $amount =  Yii::$app->request->post('amount');
        $cost_id = Yii::$app->request->post('cost_id');
        $checked = Yii::$app->request->post('checked');


        $summs = Summ::findAll(['riskId'=>$riskId, 'productId'=>$productId, 'amount'=>$amount]);
        foreach ($summs as $one_summ) {
            Summ2Interval::deleteAll('summ_id=:summ_id AND cost_id=:cost_id',[':summ_id'=>$one_summ->id, ':cost_id'=>$cost_id]);

            if ($checked==1) {
                $summ2int = new Summ2Interval();
                $summ2int->summ_id = $one_summ->id;
                $summ2int->cost_id = $cost_id;
                if (!$summ2int->save()) {
                    var_dump($summ2int->errors);
                    die();
                }
            }
        }
    }

    public function actionSaveRiskMain() {
        $riskId = Yii::$app->request->post('riskId');
        $checked = Yii::$app->request->post('checked');

        $model = $this->findModel($riskId);
        $model->main = ($checked==1)?1:0;
        if (!$model->save()) {
            var_dump($model->errors);
            die();
        }
    }

    public function actionViewTerritories($riskId, $productId, $amount) {
        $territories =  Summ::find()
            ->select(['countryId'])
            ->where(['riskId' => $riskId, 'productId'=>$productId, 'amount'=>$amount])
            ->createCommand()->queryColumn();

        $dataProvider = new ActiveDataProvider([
            'query' => Territory::find()->where(['IN', 'id_area', $territories])->indexBy('id_area'),
            'pagination' => [
                'pageSize' => -1,
            ],
        ]);

        return $this->render('view-territories', [
            'risk_model' => $this->findModel($riskId),
            'product_model' =>  Product::findOne($productId),
            'amount' => $amount,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateSumm($riskId, $productId, $amount) {
        $summ =  Summ::find()->where(['riskId' => $riskId, 'productId'=>$productId, 'amount'=>$amount])->one();

        $amounts = Yii::$app->request->post('cost_amount');
        $names = Yii::$app->request->post('cost_name');
        if (isset($amounts) && isset($names)) {
            $all_summs =  Summ::find()->where(['riskId' => $riskId, 'productId'=>$productId, 'amount'=>$amount])->all();
            foreach($all_summs as $one_summ) {
                $existing_names = array();
                foreach($amounts as $key=>$amount){
                    $name = isset($names[$key])?$names[$key]:'';
                    if ($amount>0 && $name!='') {
                        $existing_names[] = $name;
                        $summ2costModel = Summ2Cost::find()->where(['summ_id' => $one_summ->id, 'name' => $name])->one();
                        if (!$summ2costModel) {
                            $summ2costModel = new Summ2Cost();
                            $summ2costModel->summ_id = $one_summ->id;
                            $summ2costModel->name = $name;
                        }
                        $summ2costModel->amount = (int)$amount;
                        if (!$summ2costModel->save()) {
                            var_dump($summ2costModel->errors);
                            die();
                        }
                    }
                }
                Summ2Cost::deleteAll(['AND', 'summ_id=:summ_id', ['NOT IN', 'name', $existing_names]],[':summ_id' => $one_summ->id]);
            }
            $this->redirect(['view', 'id' => $riskId]);
        }
        return $this->render('update-summ', [
            'risk_model' => $this->findModel($riskId),
            'product_model' =>  Product::findOne($productId),
            'amount' => $amount,
            'summ' => $summ
        ]);
    }

    public function actionImport()
    {
    	$updated = $inserted = $deleted = 0;
	    $ids = [];
        $module  = Yii::$app->getModule('ApiLiberty');
        $products = $module->getProducts();

        foreach($products as $product) {
            $product_id = $product['productId'];
            $risks = $module->getRisks($product_id);

            Risk2Product::deleteAll(['=', 'productId', $product_id]);
            foreach ($risks as $risk) {
                $risk_id = $risk['riskId'];

                /** @var $model Risk */
                $model = Risk::find()->where(['riskId' => $risk_id])->one();
                if (!$model) {
                    $model = New Risk();
                    $inserted++;
                } else {
                    $updated++;
                }

                $model->load((array)$risk, '');
                if (!$model->save()) {
                    var_dump($model->errors);
                    die();
                }
                $ids[] = $risk_id;

                $t2p = new Risk2Product();
                $t2p->riskId = $risk_id;
                $t2p->productId = $product_id;
                $t2p->required = (int)$risk['required'];
                if (!$t2p->save()) {
                    var_dump($t2p->errors);
                    die();
                }
            }
        }

	    $deleted = Risk::deleteAll(['NOT IN', 'riskId', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);
    }

    public function actionImportSumm($riskId)
    {
        $updated = $inserted = $deleted = 0;
        $module  = Yii::$app->getModule('ApiLiberty');
        $products = $module->getProducts();

        foreach($products as $product) {
            $product_id = $product['productId'];
            $inserted_amounts = [];

            $territories = $module->getTerritories($product_id);
            foreach ($territories as $territory) {
                $territory_id = $territory['id_area'];

                $inserted_sum_ids = [];
                $curr_risk_summs = $module->getRiskSS($product_id, $riskId, $territory_id, 14);
                foreach ($curr_risk_summs as $one_summ) {
                    $amount = $one_summ['sum'];
                    $summ_model = Summ::find()->where(['riskId' => $riskId, 'productId' => $product_id, 'countryId'=> $territory_id, 'amount' => $amount])->one();
                    if (!$summ_model) {
                        $summ_model = New Summ();
                        $summ_model->riskId = $riskId;
                        $summ_model->productId = $product_id;
                        $summ_model->countryId = $territory_id;
                        $summ_model->amount = $amount;
                        if (!$summ_model->save()) {
                            var_dump($summ_model->errors);
                            die();
                        }
                        $inserted++;
                    } else {
                        $updated++;
                    }
                    $inserted_sum_ids[] = $summ_model->id;

                    if (!in_array($amount, $inserted_amounts)) $inserted_amounts[] = $amount;
                }
            }

            $deleted += Summ::deleteAll(['AND', 'productId=:product_id AND riskId=:risk_id', ['NOT IN', 'amount', $inserted_amounts]],[':product_id' => $product_id, ':risk_id'=>$riskId]);
        }

        return $this->render('import-summ', [
            'risk_model' => $this->findModel($riskId),
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
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
