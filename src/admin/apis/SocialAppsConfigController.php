<?php

namespace luya\posts\admin\apis;

class SocialAppsConfigController extends \luya\admin\base\RestController
{
    public function actionGet() {
        $mod = \Yii::$app->getModule('postsadmin');
        return [
            'fbAppId' => $mod->fbAppId,
            'vkAppId' => $mod->vkAppId,
        ];
    }
}
