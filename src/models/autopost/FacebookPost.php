<?php

namespace luya\posts\models\autopost;

use luya\posts\models\BaseAutopost;

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
