<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\InsuranceProgramm;
use common\modules\ApiAlphaStrah\models\Risk2program;
use kartik\grid\EditableColumnAction;
use Yii;
use common\modules\ApiAlphaStrah\models\Risk;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RiskController implements the CRUD actions for Risk model.
 */
class AsRiskController extends Controller
{
	public function actions()
	{
		return ArrayHelper::merge(parent::actions(), [
			'editbook' => [                                       // identifier for your editable column action
				'class' => EditableColumnAction::className(),     // action class name
				'modelClass' => Risk::className(),                // the model for the record being edited
				'showModelErrors' => true,                        // show model validation errors after save
				'errorOptions' => ['header' => '']                // error summary HTML options
			]
		]);
	}
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

		    if (isset($posted['parent_id']) && is_array($posted['parent_id'])){
			    $posted['parent_id'] = implode(',',$posted['parent_id']);
		    }

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
            'query' => Risk::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionImport()
	{
		$updated = $inserted = [];
		$deleted = 0;
		$ids = [];
		$programs = InsuranceProgramm::find()->all();

		Risk2program::deleteAll();
		foreach ($programs as $program){
			/** @var $program InsuranceProgramm*/
			$risks = Yii::$app->getModule('ApiAlphaStrah')->getRisks($program->insuranceProgrammUID);

			foreach($risks as $risk){
				$model = Risk::find()->where(['riskID' => $risk->riskID])->one();
				if (!$model){
					$model = New Risk();
					$inserted[$risk->riskID] = 1;
				} else {
					$updated[$risk->riskID] = 1;
				}
				/** @var $model Risk */
				$model->load((array)$risk, '');
				if (!$model->save()) {
					var_dump($model->errors); die();
				}

				$risk2program = new Risk2program();
				$risk2program->program_id = $program->insuranceProgrammID;
				$risk2program->risk_id = $risk->riskID;
				$risk2program->save();

				$ids[] = $model->riskID;
			}
		}

		$deleted = Risk::updateAll(['enabled' => 0], ['NOT IN', 'riskID', $ids]);

		return $this->render('import', [
			'updated' => count($updated),
			'inserted' => count($inserted),
			'deleted' => $deleted,
		]);
	}

    /**
     * Displays a single Risk model.
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
