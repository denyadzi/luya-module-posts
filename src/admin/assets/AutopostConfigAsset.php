<?php

namespace luya\posts\admin\assets;

use Yii;
use luya\web\View;

class AutopostConfigAsset extends \luya\web\Asset
{
    public $sourcePath = '@postsadmin/resources/autopost-config';

    public $js = [
        'js/directives.js'
    ];

    public $depends = [
        'luya\admin\assets\Main',
    ];
}
