/**
 * Each section of the site has its own module. It probably also has
 * submodules, though this boilerplate is too simple to demonstrate it. Within
 * `src/app/home`, however, could exist several additional folders representing
 * additional modules that would then be listed as dependencies of this one.
 * For example, a `note` section could have the submodules `note.create`,
 * `note.delete`, `note.edit`, etc.
 *
 * Regardless, so long as dependencies are managed correctly, the build process
 * will automatically take take of the rest.
 *
 * The dependencies block here is also where component dependencies should be
 * specified, as shown below.
 */
angular.module( 'ngShuttleApp.manage', [
  'ui.router',
  'ui.bootstrap'
])
.config(function config( $stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/manage');
    
    $stateProvider.state( 'manage', {
        url: '/manage',
            views: {
                "main": {
                    controller: 'ManageCtrl',
                    templateUrl: 'manage/manage.tpl.html'
                },
                "sub": {
                    controller: function(){return true;},
                   templateUrl: 'manage/overview.tpl.html'
                }
            },
            data:{ 
                pageTitle: 'Manage'
            }
    })
    .state( 'manage.shuttle',  {
        url: '/shuttle',
        views: {
                "sub": {
                    controller: 'ShuttleCtrl',
                   templateUrl: 'manage/manage.shuttle.tpl.html'
                }
            },
            data:{ 
                pageTitle: 'Manage'
            }
    });
})
.controller('ManageCtrl', ['$rootScope', '$scope', '$http', '$location', 'siteAuth', function ShuttleController($scope, $rootScope, $http, $location, siteAuth, $stopData){
    
    $scope.showStops = function($event, regionid)
    {
        if($event !== false)
        {
            angular.element(document.querySelector( 'li.active' )).removeClass('active');
            angular.element($event.currentTarget).parent().addClass('active');
        }
        $rootScope.regionid = regionid;  
        $location.path('/manage/shuttle');
    };
    
    $scope.logout = function()
    {
        siteAuth.logout();
        $location.path('/login');
    };

    
}])
.controller('ShuttleCtrl', ['$rootScope', '$scope', '$window','$modal', 'stopData', 'formFactory', function ShuttleController($scope, $rootScope, $window, $modal, stopData, formFactory){
    
    $rootScope.$watch('regionid', function(){
        stopData.getStops($rootScope.regionid).success(function(data){
            $scope.stops = data;
            //console.log($scope.stops);
        });
    });
    var modalInstance = null;
    $scope.edit = function()
    {
        formFactory.setCurrentData(this.stop);
        
        modalInstance = $modal.open({
            templateUrl: 'manage/shuttlemodal.tpl.html',
            controller: 'ModalCtrl',
            size: 'lg'
        });
        
    };
    
    $scope.save = function(form)
    {
        console.log(form);
        stopData.updateStop(form.stop.id, $rootScope.regionid, form.stop.name, form.stop.lat, form.stop.long)
        .success(function(){
            //$scope.showStops($rootScope.regionid);
            console.log($scope.stops);
            var form = formFactory.getCurrentData();
            var updatedStop = [];
            updatedStop[4]= form.stop.id;
            updatedStop[0] = form.stop.name;
            updatedStop[2] = [];
            updatedStop[2][0] = form.stop.lat;
            updatedStop[2][1] = form.stop.long;
            $scope.stops[updatedStop[4]] = updatedStop;
            console.log($scope.stops);
        });
        modalInstance.close();
    };
    
    $scope.add = function()
    {
        formFactory.setCurrentData(['',[],[],'','New']);
        
        modalInstance = $modal.open({
            templateUrl: 'manage/shuttlemodal.tpl.html',
            controller: 'ModalCtrl',
            size: 'lg'
        });
    };
    
    $scope.deleteStop = function()
    {
        $scope.sid = this.stop[4];
        modalInstance = $modal.open({
            templateUrl: 'manage/deletemodal.tpl.html',
            controller: 'DeleteModalCtrl',
            size: 'lg',
            resolve: {
                sid: function(){return $scope.sid;}
            }
        });
    };
    
    $scope.schedule = function()
    {
        $scope.sid = this.stop[4];
        modalInstance = $modal.open({
            templateUrl: 'manage/schedule.tpl.html',
            controller: 'ScheduleModalCtrl',
            resolve: {
                sid: function(){return $scope.sid;}
            }
        });
    };
    
    $scope.close = function()
    {
        modalInstance.close();
    };
    
}])
.controller('ModalCtrl', ['$scope', '$modalInstance', 'formFactory', function ModalController($scope, $modalInstance, formFactory){
    
    $scope.stop = formFactory.getCurrentData();
    $scope.form = {};
    $scope.form.stop={
      id: $scope.stop[4],
      name: $scope.stop[0],
      lat:$scope.stop[2][0],
      long:$scope.stop[2][1]
    };
    
    formFactory.setCurrentData($scope.form);
    console.log($scope.stop);
    
}])
.controller('DeleteModalCtrl', ['$scope', 'stopData', '$modalInstance', 'sid', function DeleteModalController($scope, stopData, $modalInstance, sid){
    
    $scope.yes = function(){
        var id = sid;
        delete $scope.stops[id];
        stopData.deleteStop(id)
        .success(function(){
            delete $scope.stops[id];
            $modalInstance.close();
        });
    };
    
    $scope.no = function(){
        $modalInstance.close();
    };
    
}])
.controller('ScheduleModalCtrl', ['$scope', 'sid', 'stopData', function ShuttleModalController($scope, sid, stopData){
        $scope.times = {};
        $scope.id = sid;
        console.log($scope.id);
        console.log($scope.times);
        stopData.getSchedule($scope.id)
        .success(function(data){
            if(data != "null")
            {
                $scope.times = data;
                console.log($scope.times);
            }
        });
        
        $scope.addTime = function()
        {
             
        };
}])
.factory('stopData',['$http', function stopData($http)
{
    var stops = {};
    
    stops.getStops = function(regionid)
    {
         return $http.post('./classes/controller.php', {action:'json_getstops', regionid:regionid});
    };
    
    stops.updateStop = function(u_id, u_rid, u_name, u_lat, u_long)
    {
        return $http.post('./classes/controller.php', {action:'update_insert', id:u_id, rid:u_rid, name:u_name, lat:u_lat, long:u_long});
    };
    
    stops.deleteStop = function(s_id)
    {  
       return $http.post('./classes/controller.php', {action: 'delete', id: s_id}); 
    };
    
    stops.getSchedule = function(s_id)
    {
        return $http.post('./classes/controller.php', {action:'json_getschedule', stopid:s_id});  
    };
    
    return stops;
}])
.factory('formFactory', [function formFactory(){
    
    var form = {
        data:{}
    };
    
    form.setCurrentData = function(dObj){
        this.data = dObj;
    };
    
    form.getCurrentData = function(){
        return this.data;  
    };
    
    return form;
    
}]);