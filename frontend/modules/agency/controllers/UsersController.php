<?php

namespace frontend\modules\agency\controllers;


use frontend\modules\agency\models\NewManagerForm;
use Yii;
use common\models\User;
use common\models\UserProfile;
use common\models\UserToAgency;
use common\models\Agency;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\validators\Validator;
use yii\web\Controller;

/**
 * User controller for the `agency` module
 */
class UsersController extends Controller
{



    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin_agency']
                    ]
                ]
            ]
        ];
    }

    /**
     * Renders the index view for the module
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {

        $cur_admin_agency_id = Yii::$app->user->getId();
        $data_admin_agency = Agency::getDataAgency($cur_admin_agency_id);
        $managers = User::getManagersAgency($data_admin_agency['id'],$cur_admin_agency_id);
        return $this->render('index', [
            'managers' => $managers,
            'data_admin_agency' => $data_admin_agency,
            'cur_admin_agency_id' => $cur_admin_agency_id
        ]);
    }


    /**
     * Создание пользователя агентства
     * @param $agency_id   Идентификатор самого агенства
     * @return mixed
     */
    public function actionCreate($agency_id)
    {

        $model = new NewManagerForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->scenario = NewManagerForm::SCENARIO_CREATE;
            $user = $model->signup();
            if ($user) {
                $user_to_agency = new UserToAgency();
                $user_to_agency->user_id = $user->id;
                $user_to_agency->agency_id = $agency_id;
                if($user_to_agency->save()){
                    return $this->redirect(['/agency/users/index']);
                } else {
                    throw new Exception("User couldn't be correct saved");
                }

            }
        }

        return $this->render('create', [
                'model' => $model,
        ]);

    }

    /**
     * Редактирование пользователя агентства
     * @param $user_id
     * @return mixed
     */
    public function actionUpdate($user_id)
    {

        $user = User::findOne(['id'=>$user_id]);
        $model = new NewManagerForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->scenario = NewManagerForm::SCENARIO_UPDATE;
            $validator = Validator::createValidator('unique', $model, ['username'],['targetClass'=>'\common\models\User','message' => Yii::t('frontend', 'This username has already been taken.'),'filter' => ['!=','username' ,$user->username]]);
            $validator2 = Validator::createValidator('unique', $model, ['email'],['targetClass'=>'\common\models\User','message' => Yii::t('frontend', 'This email address has already been taken.'),'filter' => ['!=','email' ,$user->email]]);
            $model->validators[] = $validator;
            $model->validators[] = $validator2;
            if($model->validate()) {
                $user = $model->update($user_id);
                if ($user) {
                    return $this->redirect(['/agency/users/index']);
                } else {
                    throw new Exception("User couldn't be correct saved");
                }
            }
        }


        return $this->render('update', [
            'model' => $model,
            'user' =>$user,
        ]);

    }

    /**
     * Удаление пользователя агентства
     * @param $user_id
     * @return mixed
     */
    public function actionDelete($user_id)
    {
        Yii::$app->authManager->revokeAll($user_id);
        $user = User::findOne(['id'=>$user_id]);
        $user->delete();
        $user_to_agency = UserToAgency::findOne(['user_id' => $user_id]);
        $user_to_agency->delete();

        return $this->redirect(['/agency/users/index']);
    }



}
