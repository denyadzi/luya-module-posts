<?php

namespace luya\posts\admin\plugins;

use Yii;
use luya\admin\ngrest\plugins\SelectArray;

class SelectOAuthPlugin extends SelectArray
{
    /**
     * @inheritdoc
     */
    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('select-oauth', $id, $ngModel, [
            'fbappid' => Yii::$app->controller->module->fbAppId,
            'initvalue' => $this->initValue,
            'options' => $this->getServiceName('selectdata'),
        ]);
    }
}
