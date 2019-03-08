<?php

use yii\db\Migration;

/**
 * Class m190301_095417_autopost_config_owner_id
 */
class m190301_095417_autopost_config_owner_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('posts_autopost_config', 'owner_id', $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('posts_autopost_config', 'owner_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190301_095417_autopost_config_owner_id cannot be reverted.\n";

        return false;
    }
    */
}
