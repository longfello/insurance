<?php

namespace backend\controllers;

use Yii;
use common\models\GeoCountry;
use common\modules\ApiTinkoff\models\Product;
use common\modules\ApiTinkoff\models\Area;
use common\modules\ApiTinkoff\models\Area2Product;
use common\modules\ApiTinkoff\models\Country;
use common\modules\ApiTinkoff\models\Country2Product;
use common\modules\ApiTinkoff\models\Country2Dict;
use common\modules\ApiTinkoff\models\Risk;
use common\modules\ApiTinkoff\models\Risk2Product;
use common\modules\ApiTinkoff\models\Price;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class TinkoffProductController extends Controller
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {

            $dataProvider = new ActiveDataProvider([
                'query' => Price::find()->where(['product_id' => $model->id])->orderBy(['TravelMedicineLimit' => SORT_ASC, 'id' => SORT_ASC]),
            ]);

            return $this->render( 'update', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ] );
        }
    }

    public function actionImport()
    {
        $processed = $updated = $inserted = $deleted = 0;
        $exist_products = [];
        $exist_areas = [];
        $exist_countries = [];
        $exist_risks = [];

        $module  = Yii::$app->getModule('ApiTinkoff');
        $products = $module->getProducts();

        foreach($products as $product){
            $update_info = false;

            $processed++;

            /** @var $model Product */
            $product_model = Product::find()->where(['Name' => $product['Name']])->one();

            if (!$product_model){
                $product_model = New Product();
                $inserted++;
                $update_info = true;
            } elseif ($product_model->ProductVersion!=$product['ProductVersion']) {
                $update_info = true;
                $updated++;
            }

            if ($update_info) {
                $product_info = $module->getProduct($product['Name']);
                $product_model->load((array)$product_info, '');
                $product_model->ProductVersion = $product['ProductVersion'];

                if (!$product_model->save()) {
                    var_dump($product_model->errors);
                    die();
                }

                foreach ($product_info['Option']['ValueInfo'] as $option) {
                    if ($option['Code']=='Area') {
                        //Сохранение регионов
                        Area2Product::deleteAll(['product_id'=>$product_model->id]);
                        foreach ($option['AvailableValue'] as $one) {
                            /** @var $area_model Area */
                            $area_model = Area::find()->where(['Value' => $one['Value']])->one();

                            if (!$area_model){
                                $area_model = New Area();
                            }

                            $area_model->load((array)$one, '');
                            if (!$area_model->save()) {
                                var_dump($area_model->errors);
                                die();
                            }

                            $a2p_model = New Area2Product();
                            $a2p_model->area_id = $area_model->id;
                            $a2p_model->product_id = $product_model->id;
                            if (!$a2p_model->save()) {
                                var_dump($a2p_model->errors);
                                die();
                            }

                            $exist_areas[] = $area_model->id;
                        }

                    } elseif($option['Code']=='AssistanceLevel') {
                        //Сохранение уровня поддержки
                        $product_model->AssistanceLevel = isset($option['AvailableValue']['Value'])?[$option['AvailableValue']]:$option['AvailableValue'];
                        if (!$product_model->save()) {
                            var_dump($product_model->errors);
                            die();
                        }
                    } elseif($option['Code']=='Currency') {
                        //Сохранени валют
                        $product_model->Currency = isset($option['AvailableValue']['Value'])?[$option['AvailableValue']]:$option['AvailableValue'];
                        if (!$product_model->save()) {
                            var_dump($product_model->errors);
                            die();
                        }
                    }
                }


                foreach ($product_info['Option']['Option'] as $option) {
                    if ($option['Code'] == 'Country') {
                        //Сохранение стран
                        Country2Product::deleteAll(['product_id'=>$product_model->id]);
                        foreach ($option['ValueInfo']['AvailableValue'] as $one) {
                            /** @var $country_model Country */
                            $country_model = Country::find()->where(['Value' => $one['Value']])->one();

                            if (!$country_model){
                                $country_model = New Country();
                            }

                            $country_model->load((array)$one, '');
                            if (!$country_model->save()) {
                                var_dump($country_model->errors);
                                die();
                            }

                            $c2p_model = New Country2Product();
                            $c2p_model->country_id = $country_model->id;
                            $c2p_model->product_id = $product_model->id;
                            if (!$c2p_model->save()) {
                                var_dump($c2p_model->errors);
                                die();
                            }

                            $c2d_count = Country2Dict::find()->where(['country_id' => $country_model->id])->count();
                            if ($c2d_count==0) {
                                /** @var $geoCountry_model GeoCountry */
                                $geoCountry_model = GeoCountry::find()->where(['iso_alpha2' => $country_model->Value])->one();
                                if ($geoCountry_model) {
                                    $c2d_model = New Country2Dict();
                                    $c2d_model->country_id = $country_model->id;
                                    $c2d_model->internal_id = $geoCountry_model->id;
                                    if (!$c2d_model->save()) {
                                        var_dump($c2d_model->errors);
                                        die();
                                    }
                                }
                            }

                            $exist_countries[] = $country_model->id;
                        }
                    } elseif ($option['Code'] == 'Coverages') {
                        //Сохранение страховых рисков
                        Risk2Product::deleteAll(['product_id'=>$product_model->id]);
                        foreach ($option['Option'] as $one_risk) {
                            /** @var $risk_model Risk */
                            $risk_model = Risk::find()->where(['Code' => $one_risk['Code']])->one();

                            if (!$risk_model){
                                $risk_model = New Risk();
                            }

                            $risk_model->load((array)$one_risk, '');
                            if (isset($one_risk['Type'])) {
                                if ($one_risk['Type'] == 'DECIMAL') {
                                    $risk_model->TypeValues = ['MinValue' => $one_risk['MinValue'], 'MaxValue' => $one_risk['MaxValue']];
                                } elseif ($one_risk['Type'] == 'LIST') {
                                    $risk_model->TypeValues = ['IsNullable' => (isset($one_risk['IsNullable']) && $one_risk['IsNullable']=='true')?1:0, 'AvailableValue' => isset($one_risk['AvailableValue']['Value'])?[$one_risk['AvailableValue']]:$one_risk['AvailableValue']];
                                }
                            }
                            if (!$risk_model->save()) {
                                var_dump($risk_model->errors);
                                die();
                            }

                            $r2p_model = New Risk2Product();
                            $r2p_model->risk_id = $risk_model->id;
                            $r2p_model->product_id = $product_model->id;
                            if (!$r2p_model->save()) {
                                var_dump($r2p_model->errors);
                                die();
                            }

                            $exist_risks[] = $risk_model->id;

                            if (isset($one_risk["ValueInfo"])) {
                                $sub_risks = isset($one_risk["ValueInfo"]['Code'])?[$one_risk["ValueInfo"]]:$one_risk["ValueInfo"];
                                foreach ($sub_risks as $sub_risk) {
                                    /** @var $subrisk_model Risk */
                                    $subrisk_model = Risk::find()->where(['Code' => $sub_risk['Code'], 'parent_id'=>$risk_model->id])->one();

                                    if (!$subrisk_model){
                                        $subrisk_model = New Risk();
                                    }

                                    $subrisk_model->load((array)$sub_risk, '');
                                    $subrisk_model->parent_id = $risk_model->id;
                                    if (isset($sub_risk['Type'])) {
                                        if ($sub_risk['Type'] == 'DECIMAL') {
                                            $subrisk_model->TypeValues = ['MinValue' => $sub_risk['MinValue'], 'MaxValue' => $sub_risk['MaxValue']];
                                        } elseif ($sub_risk['Type'] == 'LIST') {
                                            $subrisk_model->TypeValues = ['IsNullable' => (isset($sub_risk['IsNullable']) && $sub_risk['IsNullable']=='true')?1:0, 'AvailableValue' => isset($sub_risk['AvailableValue']['Value'])?[$sub_risk['AvailableValue']]:$sub_risk['AvailableValue']];
                                        }
                                    }

                                    if (!$subrisk_model->save()) {
                                        var_dump($subrisk_model->errors);
                                        die();
                                    }

                                    $r2p_model = New Risk2Product();
                                    $r2p_model->risk_id = $subrisk_model->id;
                                    $r2p_model->product_id = $product_model->id;
                                    if (!$r2p_model->save()) {
                                        var_dump($r2p_model->errors);
                                        die();
                                    }
                                    $exist_risks[] = $subrisk_model->id;
                                }
                            }
                        }
                    }
                }
            }

            $exist_products[] = $product_model->id;
        }

        $deleted = Product::deleteAll(['NOT IN', 'id', $exist_products]);
        if ($processed==($updated+$inserted)) {
            Area::deleteAll(['NOT IN', 'id', $exist_areas]);
            Country::deleteAll(['NOT IN', 'id', $exist_countries]);
            Risk::deleteAll(['NOT IN', 'id', $exist_risks]);
        }

        return $this->render('import', [
            'processed' => $processed,
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted,
        ]);

    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
