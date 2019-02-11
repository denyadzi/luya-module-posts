<?php

use yii\db\Migration;

/**
 * Class m190211_090301_rename_news_tables
 */
class m190205_090301_rename_news_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('news_article', 'posts_article');
        $this->renameTable('news_cat', 'posts_cat');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('posts_article', 'news_article');
        $this->renameTable('posts_cat', 'news_cat');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190211_090301_rename_news_tables cannot be reverted.\n";

        return false;
    }
    */
}
