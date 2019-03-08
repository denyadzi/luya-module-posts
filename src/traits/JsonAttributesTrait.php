<?php

namespace luya\posts\traits;

use yii\helpers\Json;

trait JsonAttributesTrait
{
    public function attributeJsonDecode($event, $attribute)
    {
        if ($this->{$attribute}) {
            return Json::decode($this->{$attribute});
        }
    }

    public function attributeJsonEncode($event, $attribute)
    {
        if ($this->{$attribute}) {
            return Json::encode($this->{$attribute});
        } else {
            return '';
        }
    }
}
