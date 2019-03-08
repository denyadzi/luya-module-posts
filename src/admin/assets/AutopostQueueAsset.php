<?php

namespace luya\posts\admin\assets;

class AutopostQueueAsset extends \luya\web\Asset
{
    public $sourcePath = '@postsadmin/resources/autopost-queue';

    public $js = [
        'js/worker.js',
    ];

    public $depends = [
        'luya\admin\assets\Main',
    ];
}
