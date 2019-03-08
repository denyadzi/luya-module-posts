<?php

namespace luya\posts\models\dto;

use luya\posts\traits\JsonAttributesTrait;

class AutopostFinishData extends \yii\base\Model
{
    use JsonAttributesTrait;
    
    /** @var string|array json */
    public $responseData;
    
    public function rules()
    {
        return [
            ['responseData', 'string'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => '\yii\behaviors\AttributesBehavior',
                'attributes' => [
                    'responseData' => [
                        self::EVENT_BEFORE_VALIDATE => [$this, 'attributeJsonEncode'],
                        self::EVENT_AFTER_VALIDATE => [$this, 'attributeJsonDecode'],
                    ],
                ],
            ],
        ];
    }
}
