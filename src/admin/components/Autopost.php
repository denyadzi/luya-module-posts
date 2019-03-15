<?php

namespace luya\posts\admin\components;

use Yii;
use yii\base\InvalidArgumentException;
use luya\admin\helpers\I18n;
use luya\posts\admin\exceptions\NoAutopostMessageException;
use luya\posts\models\{AutopostConfig,Article,AutopostQueueJob};
use luya\posts\models\Autopost as AutopostModel;

class Autopost extends \yii\base\BaseObject
{
    public function queuePostJobs(Article $article)
    {
        foreach ($this->loadConfigs() as $config) {
            try {
                $job = $this->createJob($article, $config);
            } catch (NoAutopostMessageException $e) {
                continue;
            }
            $this->queueJob($job);
        }
    }

    public function loadConfigs()
    {
        return AutopostConfig::find()->all();
    }

    private function createJob(Article $article, AutopostConfig $config)
    {
        $row = (new \yii\db\Query())
             ->select('teaser_text')
             ->from(Article::tableName())
             ->where(['id' => $article->id])
             ->one();
        $message = I18n::decodeFindActive($row['teaser_text'], '', $config->lang->short_code);
        if (empty($message)) {
            throw new NoAutopostMessageException();
        }
        switch ($config->type) {
        case AutopostModel::TYPE_FACEBOOK:
            return new AutopostQueueJob([
                'job_data' => [
                    'type' => AutopostModel::TYPE_FACEBOOK,
                    'articleId' => $article->id,
                    'configId' => $config->id,
                    'message' => $message,
                    'link' => $article->getDetailAbsoluteUrl(),
                    'postLink' => (bool)$config->with_link,
                    'postMessage' => (bool)$config->with_message,
                ],
            ]);
        case AutopostModel::TYPE_VK_ACCOUNT:
            return new AutopostQueueJob([
                'job_data' => [
                    'type' => AutopostModel::TYPE_VK_ACCOUNT,
                    'articleId' => $article->id,
                    'configId' => $config->id,
                    'ownerId' => $config->owner_id,
                    'message' => $message,
                    'link' => $article->getDetailAbsoluteUrl(),
                    'postLink' => (bool)$config->with_link,
                    'postMessage' => (bool)$config->with_message,
                ],
            ]);
        default:
            throw new InvalidArgumentException();
        }
    }

    public function queueJob($job)
    {
        $job->save();
    }
}
