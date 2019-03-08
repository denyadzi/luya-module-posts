<?php

use yii\db\Migration;

/**
 * Class m190308_135234_autopost_config_null_access_token
 */
class m190308_135234_autopost_config_null_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('posts_autopost_config', 'access_token', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('posts_autopost_config', 'access_token', $this->text()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190308_135234_autopost_config_null_access_token cannot be reverted.\n";

        return false;
    }
    */
}
