zaa.directive("selectOauth", function() {
  return {
    restrict: "E",
    scope: {
      "model": "=",
      "options": "=",
      "optionsvalue" : "@optionsvalue",
      "optionslabel" : "@optionslabel",
      "fbAppId": "@fbappid",
      "label": "@label",
      "i18n": "@i18n",
      "id": "@fieldid",
      "initvalue": "@initvalue"
    },
    controller: ['$scope', '$timeout', '$rootScope', 'AdminToastService', '$http', function($scope, $timeout, $rootScope, AdminToastService, $http) {

      /* default scope values */

      $scope.isOpen = 0;

      if ($scope.optionsvalue == undefined) {
        $scope.optionsvalue = 'value';
      }

      if ($scope.optionslabel == undefined) {
        $scope.optionslabel = 'label';
      }

      if (angular.isNumber($scope.model)){
	$scope.model = typeCastValue($scope.model);
      }

      /* listeners */

      $scope.$on('closeAllSelects', function() {
        if ($scope.isOpen) {
          $scope.closeSelect();
        }
      });

      $timeout(function(){
        $scope.$watch(function() { return $scope.model }, function(n, o) {
          if (n == undefined || n == null || n == '') {
            if (angular.isNumber($scope.initvalue)) {
              $scope.initvalue = typeCastValue($scope.initvalue);
            }
            var exists = $scope.valueExistsInOptions(n);

            if (!exists) {
              $scope.model = $scope.initvalue;
            }
          }
          else if (n == 'account_vk') {
            $rootScope.$broadcast('showVkFields');
          }
        });
      });

      /* methods */

      $scope.valueExistsInOptions = function(value) {
        var exists = false;
        angular.forEach($scope.options, function(item) {
          if (value == item.value) {
            exists = true;
          }
        });
        return exists;
      };

      $scope.toggleIsOpen = function() {
        if (!$scope.isOpen) {
          $rootScope.$broadcast('closeAllSelects');
        }
        $scope.isOpen = !$scope.isOpen;
      };

      $scope.closeSelect = function() {
        $scope.isOpen = 0;
      };

      $scope.setModelValue = function(option) {
        $scope.model = option[$scope.optionsvalue];
        $scope.closeSelect();
      };

      $scope.getSelectedLabel = function() {
        var defaultLabel = i18n['ngrest_select_no_selection'];
        angular.forEach($scope.options, function(item) {
          if ($scope.model == item[$scope.optionsvalue]) {
            defaultLabel = item[$scope.optionslabel];
          }
        });

        return defaultLabel;
      };

      $scope.hasSelectedValue = function() {
        var modelValue = $scope.model;

        if ($scope.valueExistsInOptions(modelValue) && modelValue != $scope.initvalue) {
          return true;
        }

        return false;
      };
    }],
    template: function() {
                return '<div class="form-group form-side-by-side" ng-class="{\'input--hide-label\': i18n}">' +
                            '<div class="form-side form-side-label">' +
                                '<label for="{{id}}">{{label}}</label>' +
                            '</div>' +
                            '<div class="form-side">' +
                                '<div class="zaaselect" ng-class="{\'open\':isOpen, \'selected\':hasSelectedValue()}">' +
                                    '<select class="zaaselect-select" ng-model="model">' +
                                        '<option ng-repeat="opt in options" ng-value="opt[optionsvalue]">{{opt[optionslabel]}}</option>' +
                                    '</select>' +
                                    '<div class="zaaselect-selected">' +
                                        '<span class="zaaselect-selected-text" ng-click="toggleIsOpen()">{{getSelectedLabel()}}</span>' +
                                        '<i class="material-icons zaaselect-clear-icon" ng-click="model=initvalue">clear</i>' +
                                        '<i class="material-icons zaaselect-dropdown-icon" ng-click="toggleIsOpen()">keyboard_arrow_down</i>' +
                                    '</div>' +
                                    '<div class="zaaselect-dropdown">' +
                                        '<div class="zaaselect-search">' +
                                            '<input class="zaaselect-search-input" type="search" focus-me="isOpen" ng-model="searchQuery" />' +
                                        '</div>' +
                                        '<div class="zaaselect-overflow">' +
                                            '<div class="zaaselect-item" ng-repeat="opt in options | filter:searchQuery">' +
                                                '<span class="zaaselect-label" ng-class="{\'zaaselect-label-active\': opt[optionsvalue] == model}" ng-click="opt[optionsvalue] == model ? false : setModelValue(opt)">{{opt[optionslabel]}}</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                           '</div>' +
                        '</div>';
    }
  }
});

zaa.directive("hidableText", function(){
  return {
    restrict: "E",
    scope: {
      "model": "=",
      "options": "=",
      "label": "@label",
      "i18n": "@i18n",
      "id": "@fieldid",
      "placeholder": "@placeholder",
      "autocomplete" : "@autocomplete",
      "showEvent": "@showevent",
    },
    controller: ['$scope', '$rootScope', function($scope, $rootScope) {
      $scope.isShown = 0;
      if (! $scope.showEvent) {
        $scope.showEvent = 'showHidable';
      }
      $rootScope.$on($scope.showEvent, function() {
        $scope.isShown = 1;
      });
    }],
    template: function() {
      return '<div ng-show="isShown == 1" class="form-group form-side-by-side" ng-class="{\'input--hide-label\': i18n}"><div class="form-side form-side-label"><label for="{{id}}">{{label}}</label></div><div class="form-side"><input id="{{id}}" insert-paste-listener ng-model="model" type="text" class="form-control" autocomplete="{{autocomplete}}" placeholder="{{placeholder}}" /></div></div>';
    }
  }
});

    /**
     * options = {'true-value' : 1, 'false-value' : 0};
     */
    zaa.directive("tempCheckbox", function() {
        return {
            restrict: "E",
            scope: {
                "model": "=",
                "options": "=",
                "i18n": "@i18n",
                "id": "@fieldid",
                "label": "@label",
                "initvalue": "@initvalue"
            },
            controller: ['$scope', '$timeout', function($scope, $timeout) {
                if ($scope.options === null || $scope.options === undefined) {
                    $scope.valueTrue = 1;
                    $scope.valueFalse = 0;
                } else {
                    $scope.valueTrue = $scope.options['true-value'];
                    $scope.valueFalse = $scope.options['false-value'];
                }

                $scope.init = function() {
            		if ($scope.model == undefined && $scope.model == null) {
            		  $scope.model = typeCastValue($scope.initvalue);
            		}
                };
                $timeout(function() {
                	$scope.init();
            	})
            }],
            template: function() {
                return  '<div class="form-group form-side-by-side" ng-class="{\'input--hide-label\': i18n}">' +
                            '<div class="form-side form-side-label">' +
                                '<label for="{{id}}">{{label}}</label>' +
                            '</div>' +
                            '<div class="form-side">' +
                                '<div class="form-check">' +
                                    '<input id="{{id}}" ng-true-value="{{valueTrue}}" ng-false-value="{{valueFalse}}" ng-model="model" type="checkbox" class="form-check-input-standalone" ng-checked="model == valueTrue"/>' +
                                    '<label for="{{id}}"></label>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
            }
        }
    });

