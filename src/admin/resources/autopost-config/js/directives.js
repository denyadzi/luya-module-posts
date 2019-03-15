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
      return '<div ng-if="isShown == 1" class="form-group form-side-by-side" ng-class="{\'input--hide-label\': i18n}"><div class="form-side form-side-label"><label for="{{id}}">{{label}}</label></div><div class="form-side"><input id="{{id}}" insert-paste-listener ng-model="model" type="text" class="form-control" autocomplete="{{autocomplete}}" placeholder="{{placeholder}}" /></div></div>';
    }
  }
});
