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
    controller: ['$scope', '$timeout', '$rootScope', 'AdminToastService', function($scope, $timeout, $rootScope, AdminToastService) {

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

      $scope.pageTokens = [];
      $scope.selectedPageToken = null;
      $scope.showPageSelect = false;

      /* listeners */

      $scope.$on('closeAllSelects', function() {
        if ($scope.isOpen) {
          $scope.closeSelect();
        }
      });
      $scope.$on('renewOAuthToken', function() {
        if ($scope.model == 'facebook') {
          fbLogin();
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
          else if (n == 'facebook' && n !== o) {
            fbLogin();
          }
        });
        $scope.$watch('selectedPageToken', function(n, o) {
          if (n && n !== o) {
            $rootScope.oauthToken = n;
            $rootScope.$broadcast('setOAuthToken');
          }
        });
      });

      function fbLogin() {
        if (typeof FB !== 'undefined') {
          FB.login(fbLoginCallback, {
            auth_type: 'reauthenticate',
            scope: 'manage_pages,publish_pages',
          });
        }
      }
      function fbLoginCallback(response) {
        if (response.authResponse) {
          FB.api('/me/accounts', function(response) {
            $scope.pageTokens = response.data;
            $scope.showPageSelect = true;
          });
        } else {
          AdminToastService.error(i18n['js_autopost_config_fb_login_fail']);
        }
      }

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
      
      /* init code */
      
      window.fbAsyncInit = function() {
        FB.init({
          appId            : $scope.fbAppId,
          autoLogAppEvents : true,
          xfbml            : true,
          version          : 'v3.2'
        });
      };

      (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
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
                           '<zaa-select initvalue="0" options="pageTokens" model="selectedPageToken" optionsvalue="access_token" optionslabel="name" i18n="" label="" ng-show="showPageSelect"></zaa-select>' +
                        '</div>';
    }
  }
});

zaa.directive("oauthToken", function() {
  return {
    restrict: "E",
    scope: {
      "model": "=",
      "label": "@label",
      "i18n": "@i18n",
      "id": "@fieldid",
    },
    controller: ['$scope', '$rootScope', function($scope, $rootScope) {
      $rootScope.$on('setOAuthToken', function() {
        $scope.model = $rootScope.oauthToken;
      });
      $scope.renewToken = function() {
        $rootScope.$broadcast('renewOAuthToken');
        return false;
      }
    }],
    template: function() {
      return '<div class="form-group form-side-by-side">' +
               '<input type="hidden" ng-model="model" name="{{ id }}" id="{{ id }}"/>' +
               '<a class="btn" ng-click="renewToken()">' +
                 i18n['js_autopost_config_label_renew_token'] +
               '</a>' +
             '</div>';
    },
  };
});
