<?php

namespace luya\posts\models;

use Yii;
use yii\helpers\ArrayHelper;
use luya\admin\ngrest\base\NgRestModel;
use luya\posts\admin\Module;
use luya\posts\admin\jobs\ResetAutopostReserve;
use luya\posts\traits\JsonAttributesTrait;
use luya\posts\models\{Article,AutopostConfig,AutopostQueueJobQuery};

/**
 * Autopost Queue Job.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $config_id
 * @property text $job_data
 * @property integer $timestamp_reserve
 * @property integer $timestamp_finish
 * @property integer $timestamp_create
 * @property integer $timestamp_update
 */
class AutopostQueueJob extends NgRestModel
{
    use JsonAttributesTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts_autopost_queue_job';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-posts-autopostqueuejob';
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new AutopostQueueJobQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function ngRestFind()
    {
        return new AutopostQueueJobQuery(get_called_class());
    }
    
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'queueResetReserveJob']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'queueResetReserveJob']);
    }

    public function queueResetReserveJob()
    {
        if ($this->timestamp_reserve && ! $this->timestamp_finish) {
            Yii::$app->adminqueue->delay(3 * 60)->push(new ResetAutopostReserve(['autopostJobId' => $this->id]));
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => '\yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'timestamp_create',
                'updatedAtAttribute' => 'timestamp_update',
            ],
            [
                'class' => '\yii\behaviors\AttributesBehavior',
                'attributes' => [
                    'job_data' => [
                        self::EVENT_AFTER_FIND => [$this, 'attributeJsonDecode'],
                        self::EVENT_BEFORE_UPDATE => [$this, 'attributeJsonEncode'],
                        self::EVENT_BEFORE_INSERT => [$this, 'attributeJsonEncode'],
                        self::EVENT_BEFORE_VALIDATE => [$this, 'attributeJsonEncode'],
                        self::EVENT_AFTER_VALIDATE => [$this, 'attributeJsonDecode'],
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('autopost_queue_job_id'),
            'article_id' => Module::t('autopost_queue_job_article_id'),
            'config_id' => Module::t('autopost_queue_job_config_id'),
            'job_data' => Module::t('autopost_queue_job_data'),
            'timestamp_finish' => Module::t('autopost_queue_job_timestamp_finish'),
            'timestamp_reserve' => Module::t('autopost_queue_job_timestamp_reserve'),
            'timestamp_create' => Module::t('autopost_queue_job_timestamp_create'),
            'timestamp_update' => Module::t('autopost_queue_job_timestamp_update'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_data'], 'required'],
            [['job_data'], 'string'],
            [['timestamp_finish', 'timestamp_reserve', 'article_id', 'config_id'], 'integer'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
            [['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => AutopostConfig::className(), 'targetAttribute' => ['config_id' => 'id']],        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'job_data' => 'hidden',
            'article_id' => 'number',
            'config_id' => 'number',
            'timestamp_finish' => 'number',
            'timestamp_create' => 'hidden',
            'timestamp_update' => 'hidden',
            'timestamp_reserve' => 'number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['job_data', 'timestamp_finish', 'timestamp_create', 'timestamp_update']],
            ['delete', true],
        ];
    }
}
