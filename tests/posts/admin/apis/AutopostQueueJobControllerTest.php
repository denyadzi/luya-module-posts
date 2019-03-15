<?php

namespace luya\posts\tests\admin\apis;

class AutopostQueueJobControllerTest extends \luya\testsuite\cases\NgRestTestCase
{
    public $modelClass = 'luya\posts\models\AutopostQueueJob';
    public $apiClass = 'luya\posts\admin\apis\AutopostQueueJobController';
    public $modelFixtureData = [
        'model1' => [
            'id' => 1,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => null,
            'timestamp_finish' => null,
        ],
        'model2' => [
            'id' => 2,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => null,
            'timestamp_finish' => null,
        ],
        'model3' => [
            'id' => 3,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => 1552024475,
            'timestamp_finish' => 1552024479,
        ],
    ];

    public function getConfigArray()
    {
        return [
            'id' => 'queuejobtest',
            'basePath' => dirname(__DIR__),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'adminLanguage' => [
                    'class' => 'luya\admin\components\AdminLanguage',
                ],
                'urlManager' => [
                    'class' => 'luya\web\UrlManager',
                    'baseUrl' => 'http://localhost/',
                ],
            ],
            'modules' => [
                'postsadmin' => [
                    'class' => 'luya\posts\admin\Module',
                ],
            ],
        ];
    }

    public function testActionPending()
    {
        $pendingJobs = $this->api->runAction('pending');

        $this->assertEquals(2, count($pendingJobs));
    }
}
