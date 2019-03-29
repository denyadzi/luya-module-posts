<?php

namespace luya\posts\admin\render;

use luya\posts\admin\render\RenderArticleCrudView;

class RenderArticleCrud extends \luya\admin\ngrest\render\RenderCrud
{
    private $_myview;
    
    /**
     * @TODO `renderCrudView` property
     * @TODO `_view` property protected
     * 
     * @inheritdoc
     */
    public function getView()
    {
        if ($this->_myview === null) {
            $this->_myview = new RenderArticleCrudView();
        }

        return $this->_myview;
    }
}
