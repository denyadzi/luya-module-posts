<?php

use yii\db\Migration;

/**
 * Class m190206_162042_auto_post_model
 */
class m190206_162042_auto_post_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts_autopost', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'type' => $this->string(32)->notNull(),
            'post_data' => $this->text(),
            'timestamp_create' => $this->integer(11),
            'timestamp_update' => $this->integer(11),
            'article_id' => $this->integer(11)->notNull(),
        ]);
        $this->addForeignKey('fk_article_id',
                             'posts_autopost',
                             'article_id',
                             'posts_article',
                             'id',
                             'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_article_id', 'posts_autopost');
        $this->dropTable('posts_autopost');
    }
}
