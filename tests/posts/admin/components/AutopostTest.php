<?php

namespace luya\posts\tests\admin\components;

use luya\admin\components\AdminLanguage;
use luya\posts\admin\components\Autopost;
use luya\posts\admin\jobs\FacebookAutopost;
use luya\posts\models\{AutopostConfig,AutopostQueueJob};
use luya\posts\models\Autopost as AutopostModel;

class AutopostTest extends \poststests\BaseWebTestCase
{
    public function testQueuePostJobs_articleHasFbConfigLanguageTeaserText_isQueued()
    {
        $article = $this->articleFixture->getModel('model1');
        $comp = $this->getMockBuilder(Autopost::className())
              ->setMethods(['loadConfigs', 'queueJob'])
              ->getMock();
        $comp
            ->expects($this->once())
            ->method('loadConfigs')
            ->willReturn([ new AutopostConfig([
                'id' => 1,
                'lang_id' => 1,
                'type' => 'facebook',
                'access_token' => '1234',
                'with_link' => 1,
            ]) ]);
        $comp
            ->expects($this->once())
            ->method('queueJob')
            ->with($this->callback(function($job) {
                return is_a($job, FacebookAutopost::className());
            }));

        $comp->queuePostJobs($article);
    }

    public function testQueuePostJobs_articleHasNoConfigLanguageTeaserText_isNotQueued()
    {
        $article = $this->articleFixture->getModel('model1');
        $comp = $this->getMockBuilder(Autopost::className())
              ->setMethods(['loadConfigs', 'queueJob'])
              ->getMock();
        $comp
            ->expects($this->once())
            ->method('loadConfigs')
            ->willReturn([ new AutopostConfig([
                'id' => 1,
                'lang_id' => 2,
                'type' => 'facebook',
                'access_token' => '1234',
                'with_link' => 1,
            ]) ]);
        $comp
            ->expects($this->never())
            ->method('queueJob');
        
        $comp->queuePostJobs($article);
    }

    public function testQueuePostJobs_articleHasVkAcntConfigLanguageTeaserText_isQueued()
    {
        $article = $this->articleFixture->getModel('model1');
        $comp = $this->getMockBuilder(Autopost::className())
              ->setMethods(['loadConfigs', 'queueJob'])
              ->getMock();
        $comp
            ->expects($this->once())
            ->method('loadConfigs')
            ->willReturn([ new AutopostConfig([
                'id' => 1,
                'lang_id' => 1,
                'type' => 'account_vk',
                'owner_id' => '1234',
                'with_link' => 1,
            ]) ]);
        $comp
            ->expects($this->once())
            ->method('queueJob')
            ->with($this->callback(function($job) {
                return is_a($job, AutopostQueueJob::className());
            }));

        $comp->queuePostJobs($article);
    }
    

    public function testCreateJob_fb()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'id' => 2,
            'type' => AutopostModel::TYPE_FACEBOOK,
            'access_token' => '1234',
            'with_link' => 1,
            'lang_id' => 1,
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);

        $this->assertSame('Teaser 1', $job->message);
        $this->assertSame('http://localhost/posts/default/detail?id=1&title=title-1', $job->link);
        $this->assertSame('1234', $job->accessToken);
        $this->assertEquals(1, $job->articleId);
        $this->assertEquals(2, $job->configId);
        $this->assertTrue($job->postLink);
        $this->assertFalse($job->postMessage);
    }

    public function testCreateJob_vkAcnt()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'id' => 2,
            'type' => AutopostModel::TYPE_VK_ACCOUNT,
            'owner_id' => '1234',
            'with_link' => 1,
            'lang_id' => 1,
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);

        $jobData = $job->job_data;
        $this->assertTrue(is_array($jobData));
        $this->assertSame('Teaser 1', $jobData['message']);
        $this->assertSame('http://localhost/posts/default/detail?id=1&title=title-1', $jobData['link']);
        $this->assertSame('account_vk', $jobData['type']);
        $this->assertSame('1234', $jobData['ownerId']);
        $this->assertEquals(1, $jobData['articleId']);
        $this->assertEquals(2, $jobData['configId']);
        $this->assertTrue($jobData['postLink']);
        $this->assertFalse($jobData['postMessage']);
    }
    
    /**
     * @expectedException \luya\posts\admin\exceptions\NoAutopostMessageException
     */
    public function testCreateJob_fbNoMessage()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'type' => AutopostModel::TYPE_FACEBOOK,
            'lang_id' => 2, // no "de" translation
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);
    }

    /**
     * @expectedException \luya\posts\admin\exceptions\NoAutopostMessageException
     */
    public function testCreateJob_vkAcntNoMessage()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'type' => AutopostModel::TYPE_VK_ACCOUNT,
            'lang_id' => 2, // no "de" translation
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);
    }
    
    public function testCreateJob_fbWithMessageFlag()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'id' => 2,
            'type' => AutopostModel::TYPE_FACEBOOK,
            'access_token' => '1234',
            'with_link' => 0,
            'with_message' => 1,
            'lang_id' => 1,
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);

        $this->assertTrue($job->postMessage);
    }

    public function testCreateJob_vkAcntWithMessageFlag()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'id' => 2,
            'type' => AutopostModel::TYPE_VK_ACCOUNT,
            'owner_id' => '1234',
            'with_link' => 0,
            'with_message' => 1,
            'lang_id' => 1,
        ]);

        $job = $this->invokeMethod($this->app->postsautopost, 'createJob', [$article, $config]);

        $this->assertTrue($job->job_data['postMessage']);
    }
}
