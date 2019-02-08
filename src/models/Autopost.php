<?php

namespace luya\news\models;

use yii\base\InvalidArgumentException;
use luya\news\models\autopost\FacebookPost;

/**
 * @inheritdoc
 */
class Autopost extends \luya\news\models\BaseAutopost
{
    const TYPE_FACEBOOK = 'facebook';

    public static function factory($type, $config = [])
    {
        $config['type'] = $type;
        switch ($type) {
        case self::TYPE_FACEBOOK:
            return new FacebookPost($config);
        default:
            throw new InvalidArgumentException();
        }
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier() {}
    
    /**
     * @inheritdoc
     */
    public function setResponseData(array $data) {}
}
