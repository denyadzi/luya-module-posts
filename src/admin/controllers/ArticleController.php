<?php

namespace luya\posts\admin\controllers;

use Yii;
use luya\admin\ngrest\NgRest;
use luya\posts\admin\render\RenderArticleCrud;

class ArticleController extends \luya\admin\ngrest\base\Controller
{
    public $modelClass = '\luya\posts\models\Article';
    public $renderCrud = [
        'view' => '\luya\posts\admin\render\RenderArticleCrudView',
    ];
}
