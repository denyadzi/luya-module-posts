<?php

namespace luya\posts\admin\apis;

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
