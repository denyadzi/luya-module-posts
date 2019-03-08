<?php

use yii\db\Migration;

/**
 * Class m190301_074847_autopost_queue_job
 */
class m190301_074847_autopost_queue_job extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts_autopost_queue_job', [
            'id' => $this->primaryKey(),
            'job_data' => $this->text()->notNull(),
            'timestamp_reserve' => $this->integer(11),
            'timestamp_finish' => $this->integer(11),
            'timestamp_create' => $this->integer(11)->notNull(),
            'timestamp_update' => $this->integer(11),
        ]);
        $this->createIndex('timestamp_reserve', 'posts_autopost_queue_job', 'timestamp_reserve');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('timestamp_reserve', 'posts_autopost_queue_job');
        $this->dropTable('posts_autopost_queue_job');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190301_074847_autopost_queue_job cannot be reverted.\n";

        return false;
    }
    */
}
