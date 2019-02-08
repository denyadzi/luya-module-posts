<?php

namespace newstests;

use luya\testsuite\fixtures\ActiveRecordFixture;
use luya\admin\models\Lang;
use luya\news\models\{Autopost,Article,Cat,AutopostConfig};

class BaseWebTestCase extends \luya\testsuite\cases\WebApplicationTestCase
{
    /** @var ActiveRecordFixture */
    protected $autopostFixture;
    /** @var ActiveRecordFixture */
    protected $articleFixture;
    /** @var ActiveRecordFixture */
    protected $catFixture;
    /** @var ActiveRecordFixture */
    protected $langFixture;
    /** @var ActiveRecordFixture */
    protected $autopostConfigFixture;
    
    public function getConfigArray()
    {
        return [
            'id' => 'newstests',
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
                'newsadmin' => [
                    'class' => 'luya\news\admin\Module',
                ],
            ],
        ];
    }

    public function afterSetup()
    {
        $this->autopostFixture = new ActiveRecordFixture([
            'modelClass' => Autopost::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'type' => 'facebook',
                    'article_id' => 1,
                    'post_data' => '{"id": "123"}',
                ],
            ],
        ]);
        $this->catFixture = new ActiveRecordFixture([
            'modelClass' => Cat::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'title' => '{"en": "Cat 1", "de": "Kat 1"}',
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
                ],
            ],
        ]);
        $this->langFixture = new ActiveRecordFixture([
            'modelClass' => Lang::className(),
            'fixtureData' => [
                'en' => [
                    'id' => 1,
                    'name' => 'English',
                    'short_code' => 'en',
                    'is_default' => 1,
                    'is_deleted' => 0,
                ],
                'de' => [
                    'id' => 2,
                    'name' => 'Deutsch',
                    'short_code' => 'de',
                    'is_default' => 0,
                    'is_deleted' => 0,
                ],
            ],
        ]);
        $this->autopostConfigFixture = new ActiveRecordFixture([
            'modelClass' => AutopostConfig::className(),
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'type' => 'facebook',
                    'lang_id' => 1,
                    'access_token' => '1234',
                    'with_link' => 1,
                ],
            ],
        ]);
    }

    public function beforeTearDown()
    {
        $this->autopostFixture->cleanup();
        $this->autopostConfigFixture->cleanup();
        $this->articleFixture->cleanup();
        $this->catFixture->cleanup();
        $this->langFixture->cleanup();
    }
}
