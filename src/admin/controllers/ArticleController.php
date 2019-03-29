<?php

namespace luya\posts\admin\controllers;

use Yii;
use luya\admin\ngrest\NgRest;
use luya\posts\admin\render\RenderArticleCrud;

class ArticleController extends \luya\admin\ngrest\base\Controller
{
    public $modelClass = '\luya\posts\models\Article';

    /**
     * Overriden just to use custom RenderCrud.
     * @TODO `renderCrud` controller property
     * 
     * @inheritdoc
     */
    public function actionIndex($inline = false, $relation = false, $arrayIndex = false, $modelClass = false, $modelSelection = false)
    {
        $apiEndpoint = $this->model->ngRestApiEndpoint();

        $config = $this->model->getNgRestConfig();

        $userSortSettings = Yii::$app->adminuser->identity->setting->get('ngrestorder.admin/'.$apiEndpoint, false);
        
        if ($userSortSettings && is_array($userSortSettings) && $config->getDefaultOrder() !== false) {
            $config->defaultOrder = [$userSortSettings['field'] => $userSortSettings['sort']];
        }
        
        // generate crud renderer
        $crud = new RenderArticleCrud();
        $crud->setModel($this->model);
        $crud->setSettingButtonDefinitions($this->globalButtons);
        $crud->setIsInline($inline);
        $crud->setModelSelection($modelSelection);
        if ($relation && is_numeric($relation) && $arrayIndex !== false && $modelClass !== false) {
            $crud->setRelationCall(['id' => $relation, 'arrayIndex' => $arrayIndex, 'modelClass' => $modelClass]);
        }
        
        // generate ngrest object from config and render renderer
        $ngrest = new NgRest($config);
        return $ngrest->render($crud);
    }
}
