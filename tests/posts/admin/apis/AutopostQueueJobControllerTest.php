<?php

namespace luya\posts\tests\admin\apis;

use luya\testsuite\fixtures\ActiveRecordFixture;
use luya\admin\components\Auth;
use luya\posts\models\{Autopost,AutopostConfig,Article};

class AutopostQueueJobControllerTest extends \luya\testsuite\cases\NgRestTestCase
{
    public $modelClass = 'luya\posts\models\AutopostQueueJob';
    public $apiClass = 'luya\posts\admin\apis\AutopostQueueJobController';
    public $modelSchema = [
        'job_data' => 'text',
        'article_id' => 'int(11)',
        'config_id' => 'int(11)',
        'timestamp_reserve' => 'int(11)',
        'timestamp_finish' => 'int(11)',
        'timestamp_create' => 'int(11)',
        'timestamp_update' => 'int(11)',
    ];
    public $modelFixtureData = [
        'model1' => [
            'id' => 1,
            'article_id' => 1,
            'config_id' => 1,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => null,
            'timestamp_finish' => null,
        ],
        'model2' => [
            'id' => 2,
            'article_id' => 1,
            'config_id' => 1,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => null,
            'timestamp_finish' => null,
        ],
        'model3' => [
            'id' => 3,
            'article_id' => 1,
            'config_id' => 1,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => 1552024475,
            'timestamp_finish' => 1552024479,
        ],
        'model4' => [
            'id' => 4,
            'article_id' => 1,
            'config_id' => 1,
            'job_data' => '{"type": "facebook", "message": "Hello World", "link": "http://localhost/article/1", "articleId": 1, "configId": 1, "postLink": 1, "postMessage": 0}',
            'timestamp_reserve' => 1552024475,
            'timestamp_finish' => null,
        ],
    ];
    /** @var ActiveRecordFixture */
    public $autopostFixture;
    /** @var ActiveRecordFixture */
    public $autopostConfigFixture;
    /** @var ActiveRecordFixture */
    public $articleFixture;

    public function getConfigArray()
    {
        return [
            'id' => 'queuejobtest',
            'basePath' => dirname(__DIR__),
            'modules' => [
                'postsadmin' => [
                    'class' => 'luya\posts\admin\Module',
                ],
            ],
        ];
    }

    public function afterSetup()
    {
        parent::afterSetup();

        $this->autopostFixture = new ActiveRecordFixture([
            'modelClass' => Autopost::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'is_deleted' => 0,
                    'type' => 'facebook',
                    'article_id' => 1,
                    'post_data' => '{"id": "123"}',
                ],
            ],
        ]);
        $this->articleFixture = new ActiveRecordFixture([
            'modelClass' => Article::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'cat_id' => 1,
                    'title' => '{"en": "Title 1", "de": ""}',
                    'teaser_text' => '{"en": "Teaser 1", "de": ""}',
                    'text' => '{"en": "Text 1", "de": ""}',
                    'create_user_id' => 0,
                    'update_user_id' => 0,
                    'timestamp_create' => 0,
                    'timestamp_update' => 0,
                    'timestamp_display_from' => 0,
                    'timestamp_display_until' => 0,
                    'is_deleted' => 0,
                    'is_display_limit' => 0,
                    'is_draft' => 0,
                    'with_autopost' => 1,
                ],
            ],
        ]);        
        $this->autopostConfigFixture = new ActiveRecordFixture([
            'modelClass' => AutopostConfig::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'is_deleted' => 0,
                    'type' => 'facebook',
                    'lang_id' => 1,
                    'with_link' => 1,
                ],
            ],
        ]);
    }

    public function beforeTearDown()
    {
        parent::beforeTearDown();
        $this->autopostFixture->cleanup();
        $this->autopostConfigFixture->cleanup();
        $this->articleFixture->cleanup();
    }

    public function testActionPending()
    {
        $pendingJobs = $this->api->actionPending();

        $this->assertEquals(2, $pendingJobs->count);
    }

    public function testActionReserve_existingNonReserved_success()
    {
        $this->getAdminQueueMock();
        $this->apiCanUpdate();

        $ret = $this->api->actionReserve(1);

        $model = $this->modelFixture->getModel('model1');
        $this->assertEquals(1, $ret['id']);
        $this->assertFalse(empty($model->timestamp_reserve));
        $this->assertTrue(empty($model->timestamp_finish));
    }

    private function getAdminQueueMock()
    {
        $mock = $this->createMock('\yii\queue\db\Queue');
        $mock->method('delay')->willReturn($mock);
        $this->app->set('adminqueue', $mock);
        return $mock;
    }

    /**
     * @expectedException \yii\web\NotFoundHttpException
     */
    public function testActionReserve_nonExisting()
    {
        $this->apiCanUpdate();

        $ret = $this->api->actionReserve(10);
    }
    
    /**
     * @expectedException \yii\web\ConflictHttpException
     */
    public function testActionReserve_existingFinished()
    {
        $this->apiCanUpdate();

        $ret = $this->api->actionReserve(3);
    }

    /**
     * @expectedException \yii\web\ConflictHttpException
     */
    public function testActionReserve_existingReserved()
    {
        $this->apiCanUpdate();

        $ret = $this->api->actionReserve(4);
    }

    public function testActionFinish_existingReservedNotFinished_success()
    {
        $this->app->request->bodyParams = [
            'responseData' => ['id' => 1234],
        ];
        $this->apiCanUpdate();
        
        $ret = $this->api->actionFinish(4);

        $model = $this->modelFixture->getModel('model4');
        $this->assertEquals(4, $ret['id']);
        $this->assertFalse(empty($model->timestamp_reserve));
        $this->assertFalse(empty($model->timestamp_finish));
    }
}
