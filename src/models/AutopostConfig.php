<?php

namespace luya\posts\models;

use Yii;
use yii\helpers\ArrayHelper;
use luya\admin\ngrest\base\NgRestModel;
use luya\admin\traits\SoftDeleteTrait;
use luya\admin\models\Lang;
use luya\posts\models\Autopost;
use luya\posts\admin\Module;

/**
 * Autopost Config.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property boolean $is_deleted
 * @property string $type
 * @property text $access_token
 * @property int $lang_id
 * @property tinyint $with_link
 * @property tinyint $with_message
 * @property string $owner_id
 */
class AutopostConfig extends NgRestModel
{
    use SoftDeleteTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts_autopost_config';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-posts-autopostconfig';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'eventBeforeUpdate']);
        $this->on(self::EVENT_AFTER_FIND, [$this, 'eventAfterFind']);
    }

    public function eventBeforeInsert()
    {
        $this->encryptTokenIfNeeded();
    }

    public function eventBeforeUpdate()
    {
        $this->encryptTokenIfNeeded();        
    }

    private function encryptTokenIfNeeded()
    {
        $postsadmin = Yii::$app->getModule('postsadmin');
        if ($postsadmin->encryptStoredTokens) {
            $this->access_token = Yii::$app->security->encryptByPassword($this->access_token, $postsadmin->encryptTokensSecret);
        }
    }

    public function eventAfterFind()
    {
        $postsadmin = Yii::$app->getModule('postsadmin');
        if ($postsadmin->encryptStoredTokens) {
            $this->access_token = Yii::$app->security->decryptByPassword($this->access_token, $postsadmin->encryptTokensSecret);
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('autopost_config_id'),
            'type' => Module::t('autopost_config_type'),
            'access_token' => Module::t('autopost_config_access_token'),
            'lang_id' => Module::t('autopost_config_lang_id'),
            'with_link' => Module::t('autopost_config_with_link'),
            'with_message' => Module::t('autopost_config_with_message'),
            'owner_id' => Module::t('autopost_config_owner_id'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang_id', 'type'], 'required'],
            [['access_token'], 'string'],
            ['access_token', 'validateAutopostParameters', 'skipOnError' => true],
            [['type'], 'string', 'max' => 32],
            [['with_link', 'with_message', 'is_deleted'], 'boolean'],
            [['with_link', 'with_message', 'is_deleted'], 'default', 'value' => false],
            [['with_message'], 'required', 'isEmpty' => function($v) { return empty($v); }, 'when' => function($model){ return empty($model->with_link); }, 'message' => Module::t('autopost_config_error_empty_link_and_message')],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['owner_id'], 'string', 'max' => 512],
        ];
    }

    public function validateAutopostParameters($attribute, $params, $validator)
    {
        $postsadmin = Yii::$app->getModule('postsadmin');
        if ($this->type == Autopost::TYPE_FACEBOOK && (empty($postsadmin->fbAppId) || empty($postsadmin->fbAppSecret))) {
            $validator->addError($this, $attribute, Module::t('autopost_config_exception_invalid_type_configuration: {type}', ['type' => $this->type]));
            return;
        }
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'access_token' => [
                'class' => 'luya\posts\admin\plugins\OAuthTokenPlugin',
            ],
            'lang_id' => [
                'selectModel',
                'modelClass' => Lang::className(),
                'labelField' => 'name',
                'valueField' => 'id',
            ],
            'type' => [
                'class' => 'luya\posts\admin\plugins\SelectOAuthPlugin',
                'data' => [
                    Autopost::TYPE_FACEBOOK => 'Facebook Page',
                    Autopost::TYPE_VK_ACCOUNT => 'Vkontakte Account',
                ],
            ],
            'owner_id' => [
                'class' => 'luya\posts\admin\plugins\HidableTextPlugin',
                'showEvent' => 'showVkFields',
            ],
            'with_link' => [
                'toggleStatus',
                'initValue' => 0, // front bug
            ],
            'with_message' => [
                'toggleStatus',
                'initValue' => 0, // front bug
            ],
        ];
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Lang::className(), ['id' => 'lang_id']);
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['type', 'lang_id', 'with_link', 'with_message']],
            [['create', 'update'], ['type', 'access_token', 'owner_id', 'lang_id', 'with_link', 'with_message']],
            ['delete', true],
        ];
    }
}
