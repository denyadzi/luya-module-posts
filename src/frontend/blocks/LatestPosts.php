<?php

namespace luya\posts\frontend\blocks;

use luya\cms\models\NavItem;
use luya\posts\models\Article;
use luya\cms\base\PhpBlock;

/**
 * Get the latest posts from the posts system.
 *
 * This block requires an application view file which is formated as followed.
 *
 * ```php
 * <?php foreach ($this->extraValue('items') as $item): ?>
 *     <?= $item->title; ?>
 * <?php endforeach; ?>
 * ```
 *
 * @author Basil Suter <basil@nadar.io>
 */
class LatestPosts extends PhpBlock
{
    private $_dropdown = [];

    public function icon()
    {
        return 'view_headline';
    }

    public function init()
    {
        foreach (NavItem::fromModule('posts') as $item) {
            $this->_dropdown[] = ['value' => $item->id, 'label' => $item->title];
        }
    }

    public function name()
    {
        return 'Posts: Latest Headlines';
    }

    public function config()
    {
        return [
            'cfgs' => [
                ['var' => 'limit', 'label' => 'Posts limit', 'type' => 'zaa-text'],
                ['var' => 'nav_item_id', 'label' => 'Post nav item', 'type' => 'zaa-select', 'options' => $this->_dropdown],
            ],
        ];
    }

    public function extraVars()
    {
        return [
            'items' => Article::getAvailable($this->getCfgValue('limit', 10)),
        ];
    }

    public function admin()
    {
        return '<ul>{% for item in extras.items %}<li>{{ item.title }}</li>{% endfor %}</ul>';
    }
}
