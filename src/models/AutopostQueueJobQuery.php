<?php

namespace luya\posts\models;

class AutopostQueueJobQuery extends \luya\admin\ngrest\base\NgRestActiveQuery
{
    public function pending()
    {
        return $this
            ->andWhere('timestamp_reserve IS NULL OR timestamp_reserve = 0')
            ->andWhere('timestamp_finish IS NULL OR timestamp_finish = 0');
    }

    public function forArticle($articleId)
    {
        return $this->andWhere(['article_id' => $articleId]);
    }
}
