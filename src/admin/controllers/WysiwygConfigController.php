<?php

namespace luya\posts\admin\controllers;

/**
 * Config for module
 */
class WysiwygConfigController extends \luya\admin\base\RestController
{
    /**
     * Get text editor config from module config
     * @return array
     */
    public function actionGet() {
        $mod = \Yii::$app->getModule('postsadmin');
        return $mod->wysiwygOptions;
    }
}
