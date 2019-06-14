<?php

namespace luya\posts\admin\render;

use luya\helpers\Json;

class RenderArticleCrudView extends \luya\admin\ngrest\render\RenderCrudView
{
    /**
     * Need to run custom service
     * 
     * @TODO move config into `getAngularControllerConfig` method
     * @inheritdoc
     */
    public function registerAngularControllerScript()
    {
        $config = $this->getAngularControllerConfig();
        
        $client = 'zaa.bootstrap.register("'.$this->context->config->getHash().'", ["$scope", "$controller", "autopostQueueWorker", function($scope, $controller, autopostQueueWorker) {
			$.extend(this, $controller("CrudController", { $scope : $scope }));
			$scope.config = '.Json::htmlEncode($config).'
            autopostQueueWorker.run();
	    }]);';
        
        $this->registerJs($client, self::POS_BEGIN);
    }
}

