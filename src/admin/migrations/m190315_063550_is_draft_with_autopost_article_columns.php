<?php

use yii\db\Migration;

/**
 * Class m190315_063550_is_draft_with_autopost_article_columns
 */
class m190315_063550_is_draft_with_autopost_article_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('posts_article', 'is_draft', $this->boolean()->notNull()->defaultValue(true));
        $this->addColumn('posts_article', 'with_autopost', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('posts_article', 'is_draft');
        $this->dropColumn('posts_article', 'with_autopost');
    }
}
