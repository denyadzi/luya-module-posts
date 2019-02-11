<?php

namespace luya\posts\admin\assets;

/**
 * Asset file for tinymce
 */
class WysiwygAssets extends \luya\web\Asset
{
    public $sourcePath = '@postsadmin/resources';

    public $js = [
        'js/directives.js',
        //'js/langs/pl.js',
    ];

    public $depends = [
        'luya\admin\assets\Main',
        'luya\posts\admin\assets\TinymceAssets'
    ];
}
