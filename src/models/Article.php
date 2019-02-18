<?php

namespace luya\posts\models;

use Yii;
use yii\helpers\{Inflector,Json};
use luya\helpers\Url;
use luya\posts\admin\Module;
use luya\admin\aws\TaggableActiveWindow;
use luya\admin\ngrest\base\NgRestModel;
use luya\admin\traits\SoftDeleteTrait;
use luya\admin\traits\TaggableTrait;
use luya\posts\models\{AutopostConfig,Autopost};

/**
 * This is the model class for table "posts_article".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property integer $cat_id
 * @property string $image_id
 * @property string $image_list
 * @property string $file_list
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property integer $timestamp_create
 * @property integer $timestamp_update
 * @property integer $timestamp_display_from
 * @property integer $timestamp_display_until
 * @property integer $is_deleted
 * @property integer $is_display_limit
 * @property string $teaser_text
 * @property string $detailUrl Return the link to the detail url of a posts item.
 * @author Basil Suter <basil@nadar.io>
 */
class Article extends NgRestModel
{
    use SoftDeleteTrait, TaggableTrait;
    
    public $i18n = ['title', 'text', 'teaser_text', 'image_list'];

    private $_autopost;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts_article';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'eventBeforeUpdate']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'eventAfterInsert']);
    }

    public function eventBeforeUpdate()
    {
        $this->update_user_id = Yii::$app->adminuser->getId();
        $this->timestamp_update = time();
    }
    
    public function eventBeforeInsert($event)
    {
        $this->create_user_id = Yii::$app->adminuser->getId();
        $this->update_user_id = Yii::$app->adminuser->getId();
        $this->timestamp_update = time();
        if (empty($this->timestamp_create)) {
            $this->timestamp_create = time();
        }
        if (empty($this->timestamp_display_from)) {
            $this->timestamp_display_from = time();
        }
    }

    public function eventAfterInsert()
    {
        if ($this->_autopost) {
            Yii::$app->postsautopost->queuePostJobs($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'text'], 'required'],
            [['title', 'text', 'image_list', 'file_list', 'teaser_text'], 'string'],
            [['cat_id', 'create_user_id', 'update_user_id', 'timestamp_create', 'timestamp_update', 'timestamp_display_from', 'timestamp_display_until'], 'integer'],
            [['is_deleted', 'is_display_limit'], 'boolean'],
            [['image_id'], 'safe'],
            ['autopost', 'validateAutopostTokens', 'skipOnError' => true],
        ];
    }

    public function validateAutopostTokens($attribute, $params, $validator)
    {
        if (! $this->_autopost) {
            return;
        }
        $autopostConfig = AutopostConfig::find()->all();
        if (empty($autopostConfig)) {
            $validator->addError($this, $attribute, 'article_autopost_no_configs');
            return;
        }
        $valid = true;
        foreach ($autopostConfig as $config) {
            if (empty($config->access_token)) {
                $validator->addError($this, $attribute, 'article_autopost_config_empty_token');
                return;
            }
            
            if ($config->type == Autopost::TYPE_FACEBOOK) {
                $curl = curl_init("https://graph.facebook.com/v3.2/debug_token?input_token={$config->access_token}");
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 10, // seconds
                    CURLOPT_TIMEOUT => 15, // seconds
                ]);

                $result = curl_exec($curl);

                if (false === $result) {
                    $validator->addError($this, $attribute, 'article_autopost_check_no_response');
                    return;
                }

                $decoded = Json::decode($result);
                if (isset($decoded['error'])) {
                    $validator->addError($this, $attribute, 'article_autopost_check_error_response');
                    return;
                }
                if (! $decoded['is_valid']) {
                    $validator->addError($this, $attribute, 'article_autopost_check_invalid_token');
                    return;
                }
            }
        }
        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Module::t('article_title'),
            'text' => Module::t('article_text'),
            'teaser_text' => Module::t('teaser_text'),
            'cat_id' => Module::t('article_cat_id'),
            'image_id' => Module::t('article_image_id'),
            'timestamp_create' => Module::t('article_timestamp_create'),
            'timestamp_display_from' => Module::t('article_timestamp_display_from'),
            'timestamp_display_until' => Module::t('article_timestamp_display_until'),
            'is_display_limit' => Module::t('article_is_display_limit'),
            'image_list' => Module::t('article_image_list'),
            'file_list' => Module::t('article_file_list'),
            'autopost' => Module::t('article_autopost'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'title' => 'text',
            'teaser_text' => ['textarea', 'markdown' => true],
            'text' => [
                'class' => 'luya\posts\admin\plugins\WysiwygPlugin',
            ],
            'image_id' => 'image',
            'timestamp_create' => 'datetime',
            'timestamp_display_from' => 'date',
            'timestamp_display_until' => 'date',
            'is_display_limit' => 'toggleStatus',
            'image_list' => 'imageArray',
            'file_list' => 'fileArray',
            'cat_id' => ['selectModel', 'modelClass' => Cat::className(), 'valueField' => 'id', 'labelField' => 'title']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestExtraAttributeTypes()
    {
        return [
            'autopost' => 'toggleStatus',
        ];
    }

    public function setAutopost($autopost)
    {
        $this->_autopost = $autopost;
    }

    public function getAutopost()
    {
        return $this->_autopost;
    }

    /**
     *
     * @return string
     */
    public function getDetailUrl()
    {
        return Url::toRoute(['/posts/default/detail', 'id' => $this->id, 'title' => Inflector::slug($this->title)]);
    }

    /**
     * @return string 
     */
    public function getDetailAbsoluteUrl()
    {
        return Url::toRoute(['/posts/default/detail', 'id' => $this->id, 'title' => Inflector::slug($this->title)], true);
    }


    /**
     * Get image object.
     * 
     * @return \luya\admin\image\Item|boolean
     */
    public function getImage()
    {
    	return Yii::$app->storage->getImage($this->image_id);
    }
    
    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-posts-article';
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestAttributeGroups()
    {
        return [
            [['timestamp_create', 'timestamp_display_from', 'is_display_limit', 'timestamp_display_until'], 'Time', 'collapsed'],
            [['image_id', 'image_list', 'file_list'], 'Media'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            [['list'], ['cat_id', 'title', 'timestamp_create', 'image_id']],
            [['create'], ['cat_id', 'title', 'teaser_text', 'autopost', 'text', 'timestamp_create', 'timestamp_display_from', 'is_display_limit', 'timestamp_display_until', 'image_id', 'image_list', 'file_list']],
            [['update'], ['cat_id', 'title', 'teaser_text', 'text', 'timestamp_create', 'timestamp_display_from', 'is_display_limit', 'timestamp_display_until', 'image_id', 'image_list', 'file_list']],
            [['delete'], true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scene = parent::scenarios();
        $scene['restcreate'][] = 'autopost';
        return $scene;
    }
    
    /**
     * @inheritdoc
     */
    public function ngRestActiveWindows()
    {
        return [
            ['class' => TaggableActiveWindow::class],
        ];
    }

    /**
     *
     * @param false|int $limit
     * @return Article
     */
    public static function getAvailable($limit = false)
    {
        $q = self::find()
            ->andWhere('timestamp_display_from <= :time', ['time' => time()])
            ->orderBy('timestamp_display_from DESC');
        
        if ($limit) {
            $q->limit($limit);
        }
        
        $articles = $q->all();

        // filter if display time is limited
        foreach ($articles as $key => $article) {
            if ($article->is_display_limit) {
                if ($article->timestamp_display_until <= time()) {
                    unset($articles[$key]);
                }
            }
        }

        return $articles;
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Cat::class, ['id' => 'cat_id']);
    }
    
    /**
     * The cat name short getter.
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->cat->title;
    }

    public function extraFields()
    {
        return ['autopost'];
    }
}
