<?php

namespace luya\posts\models;

use yii\base\InvalidArgumentException;
use luya\posts\models\autopost\{FacebookPost,VkontaktePost};

/**
 * @inheritdoc
 */
class Autopost extends \luya\posts\models\BaseAutopost
{
    const TYPE_FACEBOOK   = 'facebook';
    const TYPE_VK_ACCOUNT = 'account_vk';

    public static function factory($type, $config = [])
    {
        $config['type'] = $type;
        switch ($type) {
        case self::TYPE_FACEBOOK:
            return new FacebookPost($config);
        case self::TYPE_VK_ACCOUNT:
            return new VkontaktePost($config);
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
