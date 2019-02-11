<?php

use yii\db\Migration;

/**
 * Class m190208_083039_autopost_config_model
 */
class m190208_083039_autopost_config_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts_autopost_config', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'type' => $this->string(32)->notNull(),
            'access_token' => $this->text()->notNull(),
            'lang_id' => $this->integer(11)->notNull(),
            'with_link' => $this->boolean()->notNull()->defaultValue(false),
            'with_message' => $this->boolean()->notNull()->defaultValue(false),
            'timestamp_create' => $this->integer(11),
            'timestamp_update' => $this->integer(11),
        ]);
        $this->addColumn('posts_autopost', 'config_id', $this->integer(11)->notNull());
        $this->addForeignKey('fk_autopost_config_lang',
                             'posts_autopost_config',
                             'lang_id',
                             'admin_lang',
                             'id',
                             'CASCADE');
        $this->addForeignKey('fk_autopost_config',
                             'posts_autopost',
                             'config_id',
                             'posts_autopost_config',
                             'id',
                             'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_autopost_config', 'posts_autopost');
        $this->dropForeignKey('fk_autopost_config_lang', 'posts_autopost_config');
        $this->dropColumn('posts_autopost', 'config_id');
        $this->dropTable('posts_autopost_config');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190208_083039_autopost_config_model cannot be reverted.\n";

        return false;
    }
    */
}
