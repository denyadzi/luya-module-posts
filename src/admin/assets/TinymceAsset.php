<?php

namespace luya\posts\admin\assets;

/**
 * Asset file for module
 */
class TinymceAsset extends \luya\web\Asset
{
    public $sourcePath = '@bower';

    public $js = [
        'tinymce/tinymce.js',
        'angular-ui-tinymce/src/tinymce.js'
    ];
}
