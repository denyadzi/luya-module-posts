<?php

namespace luya\news\models\autopost;

use luya\news\models\BaseAutopost;

/**
 * @inheritdoc
 */
class FacebookPost extends BaseAutopost
{
    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return isset($this->post_data['id']) ? $this->post_data['id'] : NULL;
    }

    /**
     * @inheritdoc
     */
    public function setResponseData(array $data)
    {
        $this->post_data = [
            'id' => isset($data['id']) ? $data['id'] : NULL,
        ];
    }
}
