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
        $this->createTable('news_autopost', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer(11),
            'type' => $this->string(32)->notNull(),
            'post_data' => $this->text(),
            'timestamp_create' => $this->integer(11)->defaultValue(0),
            'timestamp_update' => $this->integer(11)->defaultValue(0),
        ]);
        $this->addForeignKey('fk_article_id',
                             'news_autopost',
                             'article_id',
                             'news_article',
                             'id',
                             'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_article_id', 'news_autopost');
        $this->dropTable('news_autopost');
    }
}
