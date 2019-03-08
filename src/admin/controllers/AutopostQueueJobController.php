<?php

namespace luya\posts\admin\controllers;

/**
 * Autopost Queue Job Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class AutopostQueueJobController extends \luya\admin\ngrest\base\Controller
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\posts\models\AutopostQueueJob';
}
