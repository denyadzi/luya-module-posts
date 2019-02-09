<?php

namespace luya\news\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\admin\models\Lang;
use luya\news\models\Autopost;
use luya\news\admin\Module;

/**
 * Autopost Config.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $type
 * @property text $access_token
 * @property int $lang_id
 * @property tinyint $with_link
 * @property tinyint $with_message
 */
class AutopostConfig extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_autopost_config';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-news-autopostconfig';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_token', 'lang_id', 'type'], 'required'],
            [['access_token'], 'string'],
            [['type'], 'string', 'max' => 32],
            [['with_link', 'with_message'], 'integer'],
            [['with_message'], 'required', 'isEmpty' => function($v) { return empty($v); }, 'when' => function($model){ return empty($model->with_link); }, 'message' => Module::t('autopost_config_error_empty_link_and_message')],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['lang_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'access_token' => 'textarea',
            'lang_id' => [
                'selectModel',
                'modelClass' => Lang::className(),
                'labelField' => 'name',
                'valueField' => 'id',
            ],
            'type' => [
                'selectArray',
                'data' => [
                    Autopost::TYPE_FACEBOOK => 'Facebook',
                ],
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
            [['create', 'update'], ['type', 'access_token', 'lang_id', 'with_link', 'with_message']],
            ['delete', false],
        ];
    }
}
