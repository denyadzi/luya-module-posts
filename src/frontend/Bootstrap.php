<?php

namespace luya\posts\frontend;

use yii\base\{BaseObject,BootstrapInterface,InvalidConfigException};
use luya\web\Application;

class Bootstrap extends BaseObject implements BootstrapInterface
{
    /** @var array */
    public $moduleLangRedirects;
    /** @var int */
    public $redirectCode = 301;

    public function init()
    {
        if ($this->moduleLangRedirects && ! is_array($this->moduleLangRedirects)) {
            throw new InvalidConfigException('`moduleLangRedirects` property must be an associative array');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'handleBeforeRequest']);
    }

    public function handleBeforeRequest($event)
    {
        $request = $event->sender->request;
        if (! $request->isConsoleRequest && $this->moduleLangRedirects) {
            foreach ($this->moduleLangRedirects as $shortLangCode => $replacement) {
                $testPath = "$shortLangCode/posts";
                if (0 === strpos($request->pathInfo, ltrim($testPath, '/'))) {

                    $redirectUrl = $request->hostInfo.'/'.str_replace("posts", $replacement, $request->pathInfo);
                    $event->sender->response->redirect($redirectUrl, $this->redirectCode)->send();
                    exit;
                }
            }
        }
    }
}

