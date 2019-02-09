<?php

namespace luya\news\models;

use yii\helpers\{Json,ArrayHelper};
use luya\admin\traits\SoftDeleteTrait;
use luya\news\admin\Module;
use luya\news\models\Article;
use luya\news\models\AutopostConfig;

/**
 * This is the model class for table "news_autopost".
 *
 * @property int $id
 * @property boolean $is_deleted
 * @property int $article_id
 * @property string $type
 * @property string $post_data
 * @property int $timestamp_create
 * @property int $timestamp_update
 *
 * @property Article $article
 */
abstract class BaseAutopost extends \yii\db\ActiveRecord
{
    use SoftDeleteTrait;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_autopost';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_FIND, [$this, 'eventAfterFind']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'eventBeforeUpdate']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_BEFORE_VALIDATE, [$this, 'eventBeforeValidate']);
    }

    public function eventAfterFind()
    {
        if ($this->post_data) {
            $this->post_data = Json::decode($this->post_data);
        }
    }

    public function eventBeforeUpdate()
    {
        $this->prepareSavePostData();
    }

    public function eventBeforeInsert()
    {
        $this->prepareSavePostData();
    }

    public function eventBeforeValidate()
    {
        $this->prepareSavePostData();
    }
    
    private function prepareSavePostData()
    {
        if ($this->post_data) {
            $this->post_data = Json::encode($this->post_data);
        } else {
            $this->post_data = '';
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'article_id'], 'required'],
            [['article_id', 'timestamp_create', 'timestamp_update'], 'integer'],
            [['is_deleted'], 'boolean'],
            [['is_deleted'], 'default', 'value' => false],
            [['type'], 'string', 'max' => 32],
            [['post_data'], 'string'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
            [['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => AutopostConfig::className(), 'targetAttribute' => ['config_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('autopost_id'),
            'article_id' => Module::t('autopost_article_id'),
            'type' => Module::t('autopost_type'),
            'post_data' => Module::t('autopost_post_data'),
            'timestamp_create' => Module::t('autopost_timestamp_create'),
            'timestamp_update' => Module::t('autopost_timestamp_update'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(AutopostConfig::className(), ['id' => 'config_id']);
    }

    /**
     * Returns remote post identifier
     *
     * With this identifier one should be able delete/update the post
     * through service api
     *
     * @return mixed
     */
    public abstract function getIdentifier();
    
    /**
     * Stores necessary data from the api response
     *
     * @param array $data Decoded api response
     */
    public abstract function setResponseData(array $data);
}
