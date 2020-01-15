<?php

namespace backend\controllers;

use Yii;
use common\modules\ApiAlphaStrah\models\Assistance;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AssistanceController implements the CRUD actions for Assistance model.
 */
class AsAssistanceController extends Controller
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
     * Lists all Assistance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Assistance::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionImport()
	{
		$updated = $inserted = $deleted = 0;
		$ids = [];

		$module  = Yii::$app->getModule('ApiAlphaStrah');

		$asses = $module->getAssistance();
		foreach($asses as $ass){
			$model = Assistance::find()->where(['assistanteID' => $ass->assistanteID])->one();
			if (!$model){
				$model = New Assistance();
				$inserted++;
			} else {
				$updated++;
			}
			/** @var $model Assistance */
			$model->load((array)$ass, '');
			if (!$model->save()) {
				var_dump($model->errors); die();
			}
			$ids[] = $model->assistanteID;
		}

		$deleted = Assistance::deleteAll(['NOT IN', 'assistanteID', $ids]);

		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted,
		]);
	}

    /**
     * Displays a single Assistance model.
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
     * Deletes an existing Assistance model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Assistance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Assistance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Assistance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
