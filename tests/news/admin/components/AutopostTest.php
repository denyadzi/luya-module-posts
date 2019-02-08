<?php

namespace luya\news\tests\admin\components;

use luya\admin\components\AdminLanguage;
use luya\news\admin\components\Autopost;
use luya\news\admin\jobs\FacebookAutopost;
use luya\news\models\AutopostConfig;
use luya\news\models\Autopost as AutopostModel;

class AutopostTest extends \newstests\BaseWebTestCase
{
    public function testQueuePostJobs_articleHasConfigLanguageTeaserText_isQueued()
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

    public function testCreateJob()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'id' => 2,
            'type' => AutopostModel::TYPE_FACEBOOK,
            'access_token' => '1234',
            'with_link' => 1,
            'lang_id' => 1,
        ]);

        $job = $this->invokeMethod($this->app->newsautopost, 'createJob', [$article, $config]);

        $this->assertSame('Teaser 1', $job->message);
        $this->assertSame('http://localhost/news/default/detail?id=1&title=title-1', $job->link);
        $this->assertSame('1234', $job->accessToken);
        $this->assertEquals(1, $job->articleId);
        $this->assertEquals(2, $job->configId);
        $this->assertTrue($job->postLink);
    }

    /**
     * @expectedException \luya\news\admin\exceptions\NoAutopostMessageException
     */
    public function testCreateJob_noMessage()
    {
        $article = $this->articleFixture->getModel('model1');
        $config  = new AutopostConfig([
            'type' => AutopostModel::TYPE_FACEBOOK,
            'lang_id' => 2, // no "de" translation
        ]);

        $job = $this->invokeMethod($this->app->newsautopost, 'createJob', [$article, $config]);
    }
}
