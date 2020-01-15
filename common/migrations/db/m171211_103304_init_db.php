<?php

use yii\db\Migration;

/**
 * Class m171211_103304_init_db
 */
class m171211_103304_init_db extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        echo "Importing DB...", PHP_EOL;

        list($db_type, $db_params) = explode(':', getenv('DB_DSN'));
        list($db_host, $db_port, $db_name) = explode(';', $db_params);
        $db_host = explode('=', $db_host)[1];
        $db_port = explode('=', $db_port)[1];
        $db_name = explode('=', $db_name)[1];
        $db_username = getenv('DB_USERNAME');
        $db_password = getenv('DB_PASSWORD');

        $domain = str_replace(array('http://', 'https://'), '', getenv('FRONTEND_URL'));

        $script_path = Yii::getAlias('@console');
        $script_name = 'import_db.sh';

        shell_exec('sh ' . $script_path . DIRECTORY_SEPARATOR . $script_name . ' '. $db_username . ' ' . $db_password . ' ' . $db_host . ' ' . $db_name . ' ' . $domain);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171211_103304_init_db cannot be reverted.\n";

        return false;
    }

}
