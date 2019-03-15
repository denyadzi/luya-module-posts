<?php

use yii\db\Migration;

/**
 * Class m190315_060101_drop_access_token_storage
 */
class m190315_060101_drop_access_token_storage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('posts_autopost_config', 'access_token');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('posts_autopost_config', 'access_token', $this->text());
    }
}
