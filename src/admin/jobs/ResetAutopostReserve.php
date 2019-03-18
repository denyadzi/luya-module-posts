<?php

namespace luya\posts\admin\jobs;

use luya\posts\models\AutopostQueueJob;
use luya\posts\admin\jobs\ResetAutopostReserveException;

class ResetAutopostReserve extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    /** @var int */
    public $autopostJobId;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $job = AutopostQueueJob::findOne($this->autopostJobId);
        if (! $job) {
            throw new ResetAutopostReserveException("Job with `id={$this->autopostJobId}` not found");
        }
        if (! $job->timestamp_reserve || $job->timestamp_finish) {
            throw new ResetAutopostReserveException("Job to reset shoud be reserved and not finished `id={$this->autopostJobId}`");
        }

        $job->timestamp_reserve = null;
        if (! $job->save()) {
            throw new ResetAutopostReserveException("Failed to save job `id={$this->autopostJobId}`");
        }
    }
}
