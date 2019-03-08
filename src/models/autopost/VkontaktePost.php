<?php

namespace luya\posts\models\autopost;

use luya\posts\models\BaseAutopost;

class VkontaktePost extends BaseAutopost
{
    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return isset($this->post_data['post_id']) ? $this->post_data['post_id'] : NULL;
    }

    /**
     * @inheritdoc
     */
    public function setResponseData(array $data)
    {
        $this->post_data = [
            'post_id' => isset($data['post_id']) ? $data['post_id'] : NULL,
        ];
    }
}
