/**
 * Created by raiym on 12/18/15.
 */

var application = angular.module('FingerprintAuth', ['ngRoute']);
application.controller('userController', function ($scope, $http, $timeout, $window, userService) {
    $scope.user = {};
    $scope.isShowAlert = false;
    $scope.message = '';
    $scope.login = function (user) {
        $http.post('backend/index.php?action=login', user)
            .then(function (response) {
                if (response.data.error !== 0) {
                    $scope.isShowAlert = true;
                    $scope.message = response.data.message;
                    $timeout(function () {
                        $scope.isShowAlert = false;
                    }, 3000);
                } else {
                    $scope.user = response.data.data;
                    userService.saveUser($scope.user);
                    $window.location.href = '#/dashboard';
                    console.log($scope.user);
                }
            }, function (response) {
                $scope.isShowAlert = true;
                $timeout(function () {
                    $scope.isShowAlert = false;
                }, 3000);
                $scope.message = 'An error has occurred when trying to login. Please try again later.';
                console.log(response);
            });
    };

    $scope.signup = function (user) {
        $http.post('/backend/index.php?action=signup', user)
            .then(function (response) {
                console.log(response);
            }, function (response) {
                console.log(response);
            });
    }
    $scope.loadUser = function() {
        return userService.getUser();
    }
});

application.config(['$routeProvider', function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: 'views/welcome-message.view.html'
        })
        .when('/welcome', {
            templateUrl: 'views/welcome-message.view.html'
        })
        .when('/login', {
            templateUrl: 'views/login.view.html',
            controller: 'userController'
        })
        .when('/signup', {
            templateUrl: 'views/signup.view.html',
            controller: 'userController'
        })
        .when('/dashboard', {
            templateUrl: 'views/dashboard.view.html',
            controller: 'userController'
        })
        .otherwise({
            redirectTo: '/login'
        });

}]);

application.factory('userService', function () {
    var factory = {};
    var user = {};
    factory.saveUser = function (userToSave) {
        user = userToSave;
        console.log('User info saved');
    };
    factory.getUser = function () {
        console.log('User retrieved');
        return user;
    };

    return factory;

});