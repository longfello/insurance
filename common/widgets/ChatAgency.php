<?php

namespace common\widgets;

use common\models\Agency;
use common\models\User;
use common\models\Chat;

use Yii;
use yii\helpers\Html;
use yii\base\Widget;


/**
 * Class ChatAgency
 * @package common\widgets
 */
class ChatAgency extends Widget
{
    /**
     * @return mixed
     */
    public function run()
    {
        $cur_admin_agency_id = Yii::$app->user->getId();
        $data_agency = Agency::getDataAgency($cur_admin_agency_id);
        $items =  Chat::find()
            ->where(['id_agency'=>$data_agency['id']])
            ->orderBy('create_at ASC')
            ->all();
        
        return $this->render('chat-agency',[
            'items' => $items,
        ]);
    }

}