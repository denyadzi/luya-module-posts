<?php

namespace luya\posts\admin\jobs;

use Yii;
use yii\base\InvalidConfigException;
use yii\queue\RetryableJob;
use yii\base\BaseObject;
use yii\helpers\Json;
use luya\posts\models\Autopost;

class FacebookAutopost extends BaseObject implements RetryableJob
{
    /** @var string */
    public $accessToken;

    /** @var int */
    public $maxAttempts = 20;

    /** @var string */
    public $message;

    /** @var string */
    public $link;

    /** @var int */
    public $articleId;

    /** @var int */
    public $configId;

    /** @var bool */
    public $postLink = true;

    /** @var bool */
    public $postMessage = false;

    public function init()
    {
        if (empty($this->accessToken)) {
            throw new InvalidConfigException('Facebook autoposting job accessToken is not configured');
        }
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $response = $this->createPost();
        $autoPost = Autopost::factory(Autopost::TYPE_FACEBOOK, [
            'article_id' => $this->articleId,
            'config_id' => $this->configId,
        ]);
        $autoPost->setResponseData($response);
        if (! $autoPost->save()) {
            throw new \RuntimeException();
        }
    }

    public function createPost()
    {
        $curl = curl_init("https://graph.facebook.com/v3.2/me/feed?access_token={$this->accessToken}");
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10, // seconds
            CURLOPT_TIMEOUT => 5, // seconds
            CURLOPT_POSTFIELDS => $this->getFieldsData(),
        ]);

        $result = curl_exec($curl);

        if (false === $result) {
            throw new \RuntimeException();
        }

        $decoded = Json::decode($result);
        if (isset($decoded['error'])) {
            throw new \RuntimeException();
        }
        return $decoded;
    }

    private function getFieldsData()
    {
        $fields = ['message' => ''];
        if ($this->postMessage) {
            $fields['message'] = $this->message;
        }
        if ($this->postLink && $this->link) {
            $fields['link'] = $this->link;
        }
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }

    /**
     * @inhertidoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt <= $this->maxAttempts;
    }
}

