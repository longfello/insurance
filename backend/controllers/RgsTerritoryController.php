<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\modules\ApiRgs\models\TerritoryType;

/**
 * RgsTerritoryController implements the CRUD actions for TerritoryType model.
 */
class RgsTerritoryController extends Controller {

    /**
     * Lists all TerritoryType models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => TerritoryType::find()->indexBy('id')
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Imports TerritoryType models from api.
     * If import is successful, the browser will be redirected to the 'import' page.
     * @return string
     */
    public function actionImport() {
        set_time_limit(0);

        $module = Yii::$app->getModule('ApiRgs');
        $updated = $inserted = $deleted = 0;
        $ids = [];
        $territories = $module->territoriesFromApi;

        foreach ($territories as $territory) {
            $model = TerritoryType::find()->where(['ext_id' => $territory['ID']])->one();

            if (!$model) {
                $model = new TerritoryType();
                $inserted++;
            } else {
                $updated++;
            }

            /** @var $model TerritoryType */
            $model->load([
                'ext_id' => $territory['ID'],
                'title' => $territory['Name']
            ], '');

            if (!$model->save()) {
                var_dump($model->errors);
                exit;
            }

            $ids[] = $model->id;
        }

        $deleted = TerritoryType::deleteAll(['NOT IN', 'id', $ids]);

        return $this->render('import', [
            'updated' => $updated,
            'inserted' => $inserted,
            'deleted' => $deleted
        ]);
    }

    /**
     * Finds the TerritoryType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TerritoryType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = TerritoryType::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
