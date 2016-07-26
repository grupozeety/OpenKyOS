  var app = angular.module('testPrimer',[]);
app.factory('mmm', function(){
  var khan = 'khangeldy'
   return khan;
  });
app.controller('testContr', ['$scope','mmm', function($scope, mmm){
      $scope.testOne = 'Hello world';
      $scope.students = [
          {name:'Jani',surname:'Norway','age':'21'},
          {name:'Hege',surname:'Sweden','age':'21'},
          {name:'Kai',surname:'Denmark','age':'21'},];
      $scope.khangeldy = '';
      $scope.sers = mmm;
  }]);
   
  app.directive("salemAlem", function(scope, attrs){
    return {
            scope:{
              test: "=info"
            },
            restrict: 'E',
            template: "<div class='alert alert-success'>  {{test}} Amandos Daulet Jaksilikuli</div><br>"
    };
  })
  