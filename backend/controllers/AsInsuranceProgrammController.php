<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\Country;
use common\modules\ApiAlphaStrah\models\Price;
use common\modules\ApiAlphaStrah\models\Risk2program;
use common\modules\ApiAlphaStrah\models\StruhSum;
use Yii;
use common\modules\ApiAlphaStrah\models\InsuranceProgramm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InsuranceProgrammController implements the CRUD actions for InsuranceProgramm model.
 */
class AsInsuranceProgrammController extends Controller
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
     * Lists all InsuranceProgramm models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => InsuranceProgramm::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionImport()
	{
		$updated = $inserted = $deleted = 0;
		$ids = [];
		$programs = Yii::$app->getModule('ApiAlphaStrah')->getProgramms();
		foreach($programs as $program){
			$model = InsuranceProgramm::find()->where(['insuranceProgrammID' => $program->insuranceProgrammID])->one();
			if (!$model){
				$model = New InsuranceProgramm();
				$inserted++;
			} else {
				$updated++;
			}
			/** @var $model \common\modules\ApiAlphaStrah\models\InsuranceProgramm */
			$model->load((array)$program, '');
			if (!$model->save()) {
				var_dump($model->errors); die();
			}
			$ids[] = $model->insuranceProgrammID;
		}

		$deleted = InsuranceProgramm::deleteAll(['NOT IN', 'insuranceProgrammID', $ids]);
		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted,
		]);
	}

    /**
     * Displays a single InsuranceProgramm model.
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
     * Update a single InsuranceProgramm model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	$model = $this->findModel($id);
	    if ($model->load(Yii::$app->request->post()) && $model->save()) {
		    return $this->redirect(['index']);
	    } else {
		    $dataProvider = new ActiveDataProvider( [
			    'query' => Price::find()->where( [ 'program_id' => $model->insuranceProgrammID ] )->orderBy( [
				    'region_id' => SORT_ASC,
				    'amount_id' => SORT_ASC
			    ] ),
		    ] );

            $dataRiskProvider = new ActiveDataProvider( [
                'query' => Risk2program::find()->where( [ 'program_id' => $model->insuranceProgrammID ] )->orderBy( [
                    'risk_id' => SORT_ASC
                ] ),
            ] );

		    return $this->render( 'update', [
			    'model' => $model,
			    'dataProvider' => $dataProvider,
			    'dataRiskProvider' => $dataRiskProvider,
		    ] );
	    }
    }

    public function actionEdit($program_id, $risk_id)
    {
        $model = Risk2program::find()->where(['program_id' => $program_id, 'risk_id' => $risk_id])->one();
        $program = $this->findModel($program_id);
        if (Yii::$app->request->post()) {

            $risks = Yii::$app->request->post('risk', []);
            $model->parent_id = implode(',',$risks);
            if ($model->save()) {
                return $this->redirect([ 'update', 'id' => $program->insuranceProgrammID ]);
            }
        } else {
            $linked_risks = explode(',', $model->parent_id);

            return $this->render('edit', [
                'model' => $model,
                'program' => $program,
                'risk' => $model->getRisk()->one(),
                'linked_risks' => $linked_risks
            ]);
        }
    }

    /**
     * Finds the InsuranceProgramm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InsuranceProgramm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InsuranceProgramm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
