<?php

namespace luya\posts\admin\apis;

use Yii;
use yii\helpers\{Json,ArrayHelper};
use yii\base\{ErrorException,InvalidCallException};
use luya\posts\admin\Module;

/**
 * Autopost Config Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class AutopostConfigController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\posts\models\AutopostConfig';

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'extendFacebookToken' => ['POST'],
        ]);
    }

    public function actionExtendFacebookToken()
    {
        $this->checkAccess('update');

        $userToken = Yii::$app->getRequest()->post('user_token');
        if (empty($userToken)) {
            throw new InvalidCallException('Empty user token for extention');
        }

        $fbAppId = $this->module->fbAppId;
        $fbAppSecret = $this->module->fbAppSecret;
        $inputToken = urlencode($userToken);

        $curl = curl_init("https://graph.facebook.com/v3.2/oauth/access_token?grant_type=fb_exchange_token&client_id={$fbAppId}&client_secret={$fbAppSecret}&fb_exchange_token={$inputToken}");
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10, // seconds
            CURLOPT_TIMEOUT => 15, // seconds
        ]);

        $result = curl_exec($curl);

        if (false === $result) {
            throw new ErrorException(Module::t('autopost_config_exception_extend_fb_token_no_response'));
        }

        $decoded = Json::decode($result);
        if (isset($decoded['error']['message'])) {
            throw new ErrorException($decoded['error']['message']);
        }
        if (empty($decoded['access_token'])) {
            throw new ErrorException(Module::t('autopost_config_exception_extend_fb_token_fail'));
        }

        return [
            'long_user_access_token' => $decoded['access_token'],
        ];
    }
}
