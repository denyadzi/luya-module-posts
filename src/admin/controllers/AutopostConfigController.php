<?php

namespace luya\posts\admin\controllers;

/**
 * Autopost Config Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class AutopostConfigController extends \luya\admin\ngrest\base\Controller
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\posts\models\AutopostConfig';
}