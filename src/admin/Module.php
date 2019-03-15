<?php

namespace luya\posts\admin;

use yii\base\InvalidConfigException;
use luya\admin\components\AdminMenuBuilder;

/**
 * Posts Admin Module.
 *
 * @author Basil Suter <basil@nadar.io>
 */
final class Module extends \luya\admin\base\Module
{
    public $wysiwygOptions = [];

    public $fbAppId;

    public $vkAppId;

    public $apis = [
        'api-posts-article' => 'luya\posts\admin\apis\ArticleController',
        'api-posts-cat' => 'luya\posts\admin\apis\CatController',
        'api-posts-autopostconfig' => 'luya\posts\admin\apis\AutopostConfigController',
        'api-posts-wysiwygconfig' => 'luya\posts\admin\apis\WysiwygConfigController',
        'api-posts-socialappsconfig' => 'luya\posts\admin\apis\SocialAppsConfigController',
        'api-posts-autopostqueuejob' => 'luya\posts\admin\apis\AutopostQueueJobController',
    ];

    public $apiRules = [
        'api-posts-autopostqueuejob' => [
            'extraPatterns' => [
                'POST {id}/reserve' => 'reserve',
                'POST {id}/finish' => 'finish',
            ],
        ],
    ];

    /**
     * @inheritdoc
     */
    public function getMenu()
    {
        return (new AdminMenuBuilder($this))
            ->node('posts', 'local_library')
                ->group('posts_administrate')
                    ->itemApi('article', 'postsadmin/article/index', 'edit', 'api-posts-article')
                    ->itemApi('cat', 'postsadmin/cat/index', 'bookmark_border', 'api-posts-cat')
                    ->itemApi('autopost_config', 'postsadmin/autopost-config/index', 'tune', 'api-posts-autopostconfig')
                    ->itemApi('autopost_queue_job', 'postsadmin/autopost-queue-job/index', 'playlist_play', 'api-posts-autopostqueuejob');
    }
    /**
     * @inheritdoc
     */
    public function registerComponents()
    {
        return [
            'postsautopost' => [
                'class' => 'luya\posts\admin\components\Autopost',
            ],
        ];
    }

    public static function onLoad()
    {
        self::registerTranslation('postsadmin', '@postsadmin/messages', [
            'postsadmin' => 'postsadmin.php',
        ]);
    }
    
    /**
     * Translat posts messages.
     *
     * @param string $message
     * @param array $params
     * @return string
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('postsadmin', $message, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdminAssets() {
        return [
            'luya\posts\admin\assets\WysiwygAsset',
            'luya\posts\admin\assets\AutopostConfigAsset',
            'luya\posts\admin\assets\AutopostQueueAsset',
        ];
    }

    public function getJsTranslationMessages()
    {
        return [
            'js_autopost_config_fb_login_fail',
        ];
    }
}
