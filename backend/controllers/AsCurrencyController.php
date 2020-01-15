<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiAlphaStrah\models\Currency;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrencyController implements the CRUD actions for Currency model.
 */
class AsCurrencyController extends Controller
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
     * Lists all Currency models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Currency::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Currency model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionImport()
	{
		$updated = $inserted = $deleted = 0;
		$ids = [];
		$currencies = Yii::$app->getModule('ApiAlphaStrah')->getCurrency();
		foreach($currencies as $currency){
			$model = Currency::find()->where(['currencyID' => $currency->currencyID])->one();
			if (!$model){
				$model = New Currency();
				$inserted++;
			} else {
				$updated++;
			}
			/** @var $model Currency */
			$model->load((array)$currency, '');
			if (!$model->save()) {
				var_dump($model->errors); die();
			}
			$ids[] = $model->currencyID;
		}

		$deleted = Currency::deleteAll(['NOT IN', 'currencyID', $ids]);

		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted,
		]);
	}

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
