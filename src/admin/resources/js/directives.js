angular.module("zaa").requires.push('ui.tinymce');

zaa.directive("wysiwyg2", function () {
  return {
    restrict: "E",
    scope: {
      "model": "=",
      "options": "=",
      "label": "@label",
      "i18n": "@i18n",
      "id": "@fieldid",
      "placeholder": "@placeholder"
    },
    template: function () {
      return "<div><textarea ui-tinymce=\"tinymceOptions\" ng-model=\"model\"></textarea></div>";
    },
    controller: ['$scope', '$element', '$timeout', '$http', '$rootScope', function ($scope, $element, $timeout, $http, $rootScope) {
      $rootScope.tinymceOptions = {height: '', plugins: '', toolbar: ''};

      $http.get('admin/api-posts-wysiwygconfig/get').then(function (response) {
        $scope.tinymceOptions = response.data;
        // tinymce bug workaround
        setTimeout(function () {
          $scope.$broadcast('$tinymce:refresh');
        }, 500);
      });
    }],
  }
});
