<?php

use luya\posts\models\Article;
use luya\posts\admin\components\Autopost;

class ArticleTest extends \poststests\BaseWebTestCase
{
    public function testGetAvailable_draftsExcluded()
    {
        $articles = Article::getAvailable();

        $this->assertEquals(1, count($articles));
    }

    public function testGetAutoposts()
    {
        $withAutopost = Article::findOne(1);
        $withoutAutopost = Article::findOne(2);

        $this->assertEquals(1, $withAutopost->getAutoposts()->count());
        $this->assertEquals(0, $withoutAutopost->getAutoposts()->count());
    }

    public function testCheckAutopostTrigger_insertDraftWithAutopost_noQueuePost()
    {
        $autopost = $this->createMock(Autopost::className());
        $autopost->expects($this->never())
            ->method('queuePostJobs');
        $this->app->set('postsautopost', $autopost);
        $new = $this->articleFixture->newModel;
        $new->attributes = [
            'title' => 'New',
            'text' => 'Content',
            'with_autopost' => true,
            'is_draft' => true,
        ];
        
        $new->save();
    }
    
    public function testCheckAutopostTrigger_insertNonDraftWithNoAutopost_noQueuePost()
    {
        $autopost = $this->createMock(Autopost::className());
        $autopost->expects($this->never())
            ->method('queuePostJobs');
        $this->app->set('postsautopost', $autopost);
        $new = $this->articleFixture->newModel;
        $new->attributes = [
            'title' => 'New',
            'text' => 'Content',
            'with_autopost' => false,
            'is_draft' => false,
        ];
        
        $new->save();
    }

    public function testCheckAutopostTrigger_insertNonDraftWithAutopost_queuePost()
    {
        $new = $this->articleFixture->newModel;
        $new->attributes = [
            'title' => 'New',
            'text' => 'Content',
            'with_autopost' => true,
            'is_draft' => false,
        ];
        $autopost = $this->createMock(Autopost::className());
        $autopost->expects($this->once())
            ->method('queuePostJobs')
            ->with($this->identicalTo($new));
        $this->app->set('postsautopost', $autopost);
        
        $new->save();
    }

    public function testCheckAutopostTrigger_updateNonDraftWithAutoposts_noQueuePost()
    {
        $autopost = $this->createMock(Autopost::className());
        $autopost->expects($this->never())
            ->method('queuePostJobs');
        $this->app->set('postsautopost', $autopost);
        $nonDraft = $this->articleFixture->getModel('model1');
        $nonDraft->with_autopost = true;
        
        $nonDraft->save();
    }

    public function testCheckAutopostTrigger_updateNonDraftWithoutAutoposts_queuePost()
    {
        $autopost = $this->createMock(Autopost::className());
        $autopost->expects($this->once())
            ->method('queuePostJobs');
        $this->app->set('postsautopost', $autopost);
        $nonDraft = $this->articleFixture->getModel('model2');
        $nonDraft->with_autopost = true;
        $nonDraft->is_draft = false;
        
        $nonDraft->save();
    }

    public function testValidate_withAutopostHavingConfigs_true()
    {
        $model = $this->articleFixture->getModel('model1');
        $model->with_autopost = true;

        $this->assertTrue($model->validate());
    }

    public function testValidate_withAutopostNoConfigs_false()
    {
        $config = $this->autopostConfigFixture->getModel('model1');
        $config->delete();
        $model = $this->articleFixture->getModel('model1');
        $model->with_autopost = true;

        $this->assertFalse($model->validate());
        $this->assertNotNull($model->errors['with_autopost']);
    }
}