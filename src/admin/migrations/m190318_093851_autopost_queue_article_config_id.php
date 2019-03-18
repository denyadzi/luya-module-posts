<?php

use yii\db\Migration;

use luya\posts\models\AutopostQueueJob;

/**
 * Class m190318_093851_autopost_queue_article_config_id
 */
class m190318_093851_autopost_queue_article_config_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('posts_autopost_queue_job', 'article_id', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('posts_autopost_queue_job', 'config_id', $this->integer()->notNull()->defaultValue(0));
        foreach (AutopostQueueJob::findAll(['article_id' => 0]) as $job) {
            $jobData = $job->job_data;
            $job->article_id = $jobData['articleId'];
            $job->config_id = $jobData['configId'];
            $job->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('posts_autopost_queue_job', 'article_id');
        $this->dropColumn('posts_autopost_queue_job', 'config_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190318_093851_autopost_queue_article_config_id cannot be reverted.\n";

        return false;
    }
    */
}
