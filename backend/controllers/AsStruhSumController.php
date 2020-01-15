<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\InsuranceProgramm;
use Yii;
use common\modules\ApiAlphaStrah\models\StruhSum;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StruhSumController implements the CRUD actions for StruhSum model.
 */
class AsStruhSumController extends Controller
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
     * Lists all StruhSum models.
     * @return mixed
     */
    public function actionIndex()
    {

	    if (Yii::$app->request->post('hasEditable')) {
		    // instantiate your book model for saving
		    $id = Yii::$app->request->post('editableKey');
		    $model = StruhSum::findOne($id);

		    // store a default json response as desired by editable
		    $out = Json::encode(['output'=>'', 'message'=>'']);
		    $posted = current($_POST['StruhSum']);
		    $post = ['StruhSum' => $posted];
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
            'query' => StruhSum::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionImport()
	{
		$updated = $inserted = $deleted = 0;
		$ids = [];

		$sums = Yii::$app->getModule('ApiAlphaStrah')->getStruhSum();
		foreach($sums as $sum){
			if ($sum->valutaCode == 'EUR') {
				$hash = StruhSum::getHash((array)$sum);
				$model = StruhSum::find()->where(['hash' => $hash])->one();
				if (!$model){
					$model = New StruhSum();
					$inserted++;
				} else {
					$updated++;
				}
				/** @var $model StruhSum */
				$model->load((array)$sum, '');
				$model->hash = $hash;
				if (!$model->save()) {
					var_dump($model->errors); die();
				}
				$ids[] = $model->hash;
			} elseif ($sum->riskUID=='e041e5b7-6567-4210-8702-6a29e3fef229' && $sum->valutaCode == 'USD') {
				if (mb_stripos($sum->variant, 'руб.')!==false || mb_stripos($sum->variant, 'с франшизой')!==false) continue;

				if (!isset($sum->strahSummFrom)) {
					$sum->strahSummFrom = 1;
				}

				$hash = StruhSum::getHash((array)$sum);
				$model = StruhSum::find()->where(['hash' => $hash])->one();
				if (!$model){
					$model = New StruhSum();
					$inserted++;
				} else {
					$updated++;
				}
				/** @var $model StruhSum */
				$model->load((array)$sum, '');
				$model->hash = $hash;
				if (!$model->save()) {
					var_dump($model->errors); die();
				}
				$ids[] = $model->hash;
			}
		}

		$deleted  = StruhSum::deleteAll(['NOT IN', 'hash', $ids]);

		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted
		]);
	}

    /**
     * Displays a single StruhSum model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the StruhSum model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StruhSum the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StruhSum::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
