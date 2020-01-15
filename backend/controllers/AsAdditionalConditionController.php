<?php

namespace backend\controllers;

use common\modules\ApiAlphaStrah\models\Country;
use kartik\grid\EditableColumnAction;
use Yii;
use common\modules\ApiAlphaStrah\models\AdditionalCondition;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdditionalConditionController implements the CRUD actions for AdditionalCondition model.
 */
class AsAdditionalConditionController extends Controller
{
	public function actions()
	{
		return ArrayHelper::merge(parent::actions(), [
			'editbook' => [                                       // identifier for your editable column action
				'class' => EditableColumnAction::className(),     // action class name
				'modelClass' => AdditionalCondition::className(), // the model for the record being edited
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
     * Lists all AdditionalCondition models.
     * @return mixed
     */
    public function actionIndex()
    {
	    if (Yii::$app->request->post('hasEditable')) {
		    // instantiate your book model for saving
		    $id = Yii::$app->request->post('editableKey');
		    $model = AdditionalCondition::findOne($id);

		    // store a default json response as desired by editable
		    $out = Json::encode(['output'=>'', 'message'=>'']);
		    $posted = current($_POST['AdditionalCondition']);
		    $post = ['AdditionalCondition' => $posted];
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
            'query' => AdditionalCondition::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AdditionalCondition model.
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
		$module  = Yii::$app->getModule('ApiAlphaStrah');

		$conditions = $module->getAdditionalConditions();
		foreach($conditions as $condition){
			$model = AdditionalCondition::find()->where(['additionalConditionID' => $condition->additionalConditionID])->one();
			if (!$model){
				$model = New AdditionalCondition();
				$inserted++;
			} else {
				$updated++;
			}
			/** @var $model Country */
			$model->load((array)$condition, '');
			if (!$model->save()) {
				var_dump($model->errors); die();
			}
			$ids[] = $model->additionalConditionID;
		}

		$deleted = AdditionalCondition::deleteAll(['NOT IN', 'additionalConditionID', $ids]);

		return $this->render('import', [
			'updated' => $updated,
			'inserted' => $inserted,
			'deleted' => $deleted,
		]);
	}

    /**
     * Finds the AdditionalCondition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdditionalCondition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdditionalCondition::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
