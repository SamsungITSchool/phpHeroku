/**
 * Created by raiym on 12/18/15.
 */

var application = angular.module('FingerprintAuth', ['ngRoute']);
application.controller('UserController', function ($scope, $http) {
    $scope.user = {};
    $scope.login = function (user) {
        //$http({}).then(function (response) {
        //
        //}, function (response) {
        //
        //});
        alert('Logged in:' + user.email);
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
            controller: 'UserController'
        })
        .when('/signup', {
            templateUrl: 'views/signup.view.html',
            controller: 'UserController'
        })
        .otherwise({
            redirectTo: '/login'
        });

}]);

application.factory('UserService', function () {
    var factory = {};
    factory.login = function (user) {
        return user;
    };

    factory.signup = function (user) {

    };
    return factory;

});