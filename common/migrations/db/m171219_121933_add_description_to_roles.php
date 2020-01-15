<?php

use yii\db\Migration;

/**
 * Class m171219_121933_add_description_to_roles
 */
class m171219_121933_add_description_to_roles extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('rbac_auth_item', ['description' => "Администратор"], "name = 'administrator'");
        $this->update('rbac_auth_item', ['description' => "Менеджер"], "name = 'manager'");
        $this->update('rbac_auth_item', ['description' => "Пользователь"], "name = 'user'");
        $this->update('rbac_auth_item', ['description' => "Пользователь АПИ"], "name = 'api_user'");
        $this->update('rbac_auth_item', ['description' => "Пользователь АПИ с тестовым доступом"], "name = 'test_api_user'");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('rbac_auth_item', ['description' => ""], "1=1");
    }
}
