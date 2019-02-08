<?php

namespace luya\news\admin\apis;

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
    public $modelClass = 'luya\news\models\AutopostConfig';
}