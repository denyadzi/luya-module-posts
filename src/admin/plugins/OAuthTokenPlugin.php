<?php

namespace luya\posts\admin\plugins;

use luya\admin\ngrest\plugins\Hidden;

class OAuthTokenPlugin extends Hidden
{
    /**
     * @inheritdoc
     */
    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('oauth-token', $id, $ngModel);
    }
}
