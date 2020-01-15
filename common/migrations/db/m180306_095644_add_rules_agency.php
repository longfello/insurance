<?php

use yii\db\Migration;

/**
 * Class m180306_095644_add_rules_agency
 */
class m180306_095644_add_rules_agency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO rbac_auth_item (name, type, description, created_at) VALUES ('admin_agency',1,'Администратор агенств',CURRENT_TIMESTAMP)");
        $this->execute("INSERT INTO rbac_auth_item (name, type, description, created_at) VALUES ('manager_agency',1,'Менеджер агенства',CURRENT_TIMESTAMP)");
        $this->execute("INSERT INTO rbac_auth_item (name, type, description, created_at) VALUES ('user_agency',1,'Пользователь агенства',CURRENT_TIMESTAMP)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {  
        $this->execute("DELETE FROM rbac_auth_item WHERE name = 'admin_agency'");
        $this->execute("DELETE FROM rbac_auth_item WHERE name = 'manager_agency'");
        $this->execute("DELETE FROM rbac_auth_item WHERE name = 'user_agency'");
    }


}
