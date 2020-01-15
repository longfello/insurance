<?php

use yii\db\Migration;

/**
 * Class m171219_111356_add_api_users_roles
 */
class m171219_111356_add_api_users_roles extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO rbac_auth_item (name, type, created_at) VALUES ('api_user', 1, CURRENT_TIMESTAMP)");
        $this->execute("INSERT INTO rbac_auth_item (name, type, created_at) VALUES ('test_api_user', 1,CURRENT_TIMESTAMP)");
        $this->execute("INSERT INTO rbac_auth_item_child (parent, child) VALUES ('api_user', 'loginToBackend')");
        $this->execute("INSERT INTO rbac_auth_item_child (parent, child) VALUES ('test_api_user', 'loginToBackend')");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute("DELETE FROM rbac_auth_item_child WHERE parent IN ('api_user', 'test_api_user');");
        $this->execute("DELETE FROM rbac_auth_item WHERE name IN ('api_user', 'test_api_user');");
    }
}
