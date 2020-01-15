<?php

namespace frontend\modules\agency\controllers;

use common\models\ChatForm;
use common\models\Agency;
use common\models\Chat;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
/**
 * Default controller for the `agency` module
 */
class DefaultController extends Controller
{

    /**
     * @inheritdoc
     * @return array
     */
    public function actions()
    {
        return [
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className()
            ]
        ];
    }

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
                        'roles' => ['@']
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

        if (!Yii::$app->user->can('admin_agency')) {
            Yii::$app->user->logout();
            return $this->redirect(['/agency/default/index']);
        }

        $model = new ChatForm();

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post())) {
                $model->doc = UploadedFile::getInstance($model, 'doc');
                $model->validate();
                $errors = $model->errors;
                if(count($errors) == 0){

                        $cur_admin_agency_id = Yii::$app->user->getId();
                        $data_agency = Agency::getDataAgency($cur_admin_agency_id);
                        $message = $model->text;
                        $chat = new Chat();
                        $chat->id_agency = $data_agency['id'];
                        $chat->from_user_id = $cur_admin_agency_id;
                        $chat->message = $message;
                        $key = rand(100000,999999);
                        if($model->download($key)) {
                           $file_name = $key.'_'.$model->doc->baseName .'.'. $model->doc->extension;
                           $chat->file = $file_name;
                        }
                        if ($chat->save()) {
                            return $this->render('index',[
                                'model' => $model,
                            ]);
                        } else {

                            return $this->render('index',[
                                'model' => $model,
                                'errors' => $errors,
                            ]);
                        }

                }

//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return [
//                    'errors' => $errors,
//                    'mess' => 'ok'
//                ];
                return $this->render('index',[
                    'model' => $model,
                    'errors' => $errors,
                ]);
            }
        }


            return $this->render('index',[
                'model' => $model,
            ]);
    }

    /**
     * @param $id
     * @return $this|Response
     */
    public function actionDownloadFile($id)
    {
        if(!empty($id)) {
            $model = Chat::findOne(['id' => $id]);
            $file_name = $model->file;

            if(file_exists(Yii::getAlias('@storage').'/web/chat_files/'.$file_name)){
                return  Yii::$app->response->sendFile(Yii::getAlias('@storage').'/web/chat_files/'.$file_name);
            }else{
                return $this->redirect(['/agency/default/index']);
            }

        } else{
            return $this->redirect(['/agency/default/index']);
        }

    }



}
