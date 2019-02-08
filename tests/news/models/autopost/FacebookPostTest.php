<?php

namespace luya\news\tests\models\autopost;

use luya\news\models\Autopost;

class FacebookPostTest extends \newstests\BaseWebTestCase
{
    public function testFactory()
    {
        $post = Autopost::factory(Autopost::TYPE_FACEBOOK, [
            'article_id' => 1
        ]);

        $this->assertSame(1, $post->article_id);
        $this->assertSame(Autopost::TYPE_FACEBOOK, $post->type);
    }

    public function testSetResponseData()
    {
        $post = Autopost::factory(Autopost::TYPE_FACEBOOK);
        $post->setResponseData([
            'id' => 'abc',
        ]);

        $this->assertSame(['id' => 'abc'], $post->post_data);
    }

    public function testGetIdentifier()
    {
        $post = Autopost::factory(Autopost::TYPE_FACEBOOK);
        $post->post_data = [
            'id' => 1234,
        ];

        $this->assertSame(1234, $post->getIdentifier());
    }

    public function testPostDataLoad()
    {
        $post = $this->autopostFixture->getModel('model1');
        $this->assertSame(['id' => '123'], $post->post_data);
    }
}
