angular.module( 'ngShuttleApp', [
  'templates-app',
  'templates-common',
  'ngShuttleApp.login',
  'ngShuttleApp.manage',
  'ui.router'
])

.config( function myAppConfig () {
 
})
.controller( 'AppCtrl', function AppCtrl ( $scope, $location ) {
  $scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){
    if ( angular.isDefined( toState.data.pageTitle ) ) {
      $scope.pageTitle = toState.data.pageTitle + ' | ngShuttleApp' ;
    }
  });
}).factory('siteAuth',['$http', function siteAuth($http){
    
    var auth = {};
    
    auth.login = function(user, pass)
    {
        return $http.post('./classes/controller.php', {action:'login', username: user, password: pass});
    };
    
    auth.isLoggedIn = function()
    {
        return $http.post('./classes/controller.php', {action:'status'});      
    };
    
    auth.logout = function()
    {
      return $http.post('./classes/controller.php', {action:'logout'});
    };
    
    return auth;
}])
.run( ['$location', 'siteAuth', function run ( $location, siteAuth ) {
  siteAuth.isLoggedIn().success(function(data){
    if(data === "false")
    {
      $location.path('/login');
    }
    else
    {
      $location.path('/manage');
    }
  });
  
}])
;

