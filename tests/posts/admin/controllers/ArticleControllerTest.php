<?php

namespace luya\posts\tests\admin\controllers;

use yii\base\Action;

class ArticleControllerTest extends \luya\testsuite\cases\NgRestTestCase
{
    public $modelClass = 'luya\posts\models\Article';
    public $controllerClass = 'luya\posts\admin\controllers\ArticleController';

    public function getConfigArray()
    {
        return [
            'id' => 'articletest',
            'basePath' => dirname(__DIR__),
            'modules' => [
                'postsadmin' => [
                    'class' => 'luya\posts\admin\Module',
                ],
            ],
        ];
    }

    /**
     * @expectedException \yii\web\ForbiddenHttpException
     */
    public function testBeforeActaion_noPermission()
    {
        $controller = clone $this->controller;
        $controller->disablePermissionCheck = false;

        $controller->beforeAction(new Action('index', $controller));
    }

    public function testBeforeActaion_hasPermission()
    {
        $controller = clone $this->controller;
        $controller->disablePermissionCheck = false;

        $this->controllerCanAccess('index');
        $passed = $controller->beforeAction(new Action('index', $controller));

        $this->assertTrue($passed);
    }
}
