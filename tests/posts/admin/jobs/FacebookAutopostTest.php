<?php

namespace luya\posts\tests\admin\jobs;

use luya\posts\admin\jobs\FacebookAutopost;
use luya\posts\models\autopost\FacebookPost;

class FacebookAutopostTest extends \poststests\BaseWebTestCase
{
    public function testExecute_currentLangTitle_isSaved()
    {
        $article = $this->articleFixture->getModel('model1');
        $config = $this->autopostConfigFixture->getModel('model1');
        $job = $this->getMockBuilder(FacebookAutopost::className())
             ->setMethods(['createPost'])
             ->setConstructorArgs([ ['accessToken' => 'token', 'message' => 'Message', 'articleId' => $article->id, 'configId' => $config->id ] ])
             ->getMock();
        $job->method('createPost')
            ->willReturn([
                'id' => 123,
            ]);
        
        $job->execute(null);

        $post = FacebookPost::findOne(2);
        $this->assertNotNull($post);
        $this->assertEquals($article->id, $post->article_id);
        $this->assertEquals($config->id, $post->config_id);
    }
}
