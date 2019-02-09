<?php

namespace luya\news\admin\components;

use Yii;
use yii\base\InvalidArgumentException;
use luya\admin\helpers\I18n;
use luya\news\admin\jobs\FacebookAutopost;
use luya\news\admin\exceptions\NoAutopostMessageException;
use luya\news\models\{AutopostConfig,Article};
use luya\news\models\Autopost as AutopostModel;

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
            return new FacebookAutopost([
                'accessToken' => $config->access_token,
                'articleId' => $article->id,
                'configId' => $config->id,
                'message' => $message,
                'link' => $article->getDetailAbsoluteUrl(),
                'postLink' => (bool)$config->with_link,
                'postMessage' => (bool)$config->with_message,
            ]);
        default:
            throw new InvalidArgumentException();
        }
    }

    public function queueJob($job)
    {
        Yii::$app->adminqueue->push($job);
    }
}
