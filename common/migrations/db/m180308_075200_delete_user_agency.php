<?php

use yii\db\Migration;

/**
 * Class m180308_075200_delete_user_agency
 */
class m180308_075200_delete_user_agency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM rbac_auth_item WHERE name = 'user_agency'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("INSERT INTO rbac_auth_item (name, type, description, created_at) VALUES ('user_agency',1,'Пользователь агенства',CURRENT_TIMESTAMP)");
    }


}
