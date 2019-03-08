<?php

namespace luya\posts\admin\plugins;

class HidableTextPlugin extends \luya\admin\ngrest\plugins\Text
{
    /** @var string */
    public $showEvent;
    
    /**
     * @inheritdoc
     */
    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('hidable-text', $id, $ngModel, [
            'placeholder' => $this->placeholder,
            'showevent' => $this->showEvent,
        ]);
    }
}
