<?php

namespace luya\posts\admin\apis;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\{ConflictHttpException, NotFoundHttpException, NotAcceptableHttpException, BadRequestHttpException, ServerErrorHttpException,ForbiddenHttpException};
use luya\posts\models\{AutopostQueueJob,Autopost};
use luya\posts\models\dto\AutopostFinishData;

/**
 * Autopost Queue Job Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class AutopostQueueJobController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'luya\posts\models\AutopostQueueJob';

    
    public function actionPending()
    {
        $this->checkAccess('filter');

        $query = $this->modelClass::ngRestFind()->pending();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionReserve($id)
    {
        $this->checkAccess('update');
        
        $job = AutopostQueueJob::findOne($id);
        if (! $job) {
            throw new NotFoundHttpException();
        }
        if ($job->timestamp_reserve || $job->timestamp_finish) {
            throw new ConflictHttpException();
        }
        $job->timestamp_reserve = time();
        $job->timestamp_finish = null;
        if (! $job->save()) {
            throw new ServerErrorHttpException();
        }
        return [
            'id' => $job->id,
            'timestamp_reserve' => $job->timestamp_reserve,
        ];
    }

    public function actionFinish($id)
    {
        $this->checkAccess('update');
        
        $job = AutopostQueueJob::findOne($id);
        if (! $job) {
            throw new NotFoundHttpException();
        }
        if (! $job->timestamp_reserve) {
            throw new NotAcceptableHttpException();
        }
        if ($job->timestamp_finish) {
            throw new ConflictHttpException();
        }
        $finishData = new AutopostFinishData();
        if ($finishData->load(Yii::$app->request->post(), '') && $finishData->validate()) {
            $jobData = $job->job_data;
            $autoPost = Autopost::factory($jobData['type'], [
                'article_id' => $jobData['articleId'],
                'config_id' => $jobData['configId'],
            ]);
            $autoPost->setResponseData($finishData->responseData);
            if (! $autoPost->save()) {
                throw new ServerErrorHttpException('Autopost saving failed '.print_r($autoPost->getErrors(), true));
            }
            $job->timestamp_finish = time();
            if (! $job->save()) {
                throw new ServerErrorHttpException('Job finishing failed');
            }
            return [
                'id' => $job->id,
                'timestamp_finish' => $job->timestamp_finish,
            ];
        } else {
            throw new BadRequestHttpException();
        }
    }

    public function actionWorkerData() {
        $mod = \Yii::$app->getModule('postsadmin');
        $enabled = true;
        try {
            $this->checkAccess('filter');
        } catch (ForbiddenHttpException $e) {
            $enabled = false;
        }
        return [
            'enabled' => $enabled,
            'fbAppId' => $mod->fbAppId,
            'vkAppId' => $mod->vkAppId,
        ];
    }
    
}
