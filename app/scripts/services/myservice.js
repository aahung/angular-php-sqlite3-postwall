'use strict';

/**
 * @ngdoc service
 * @name postWallApp.Myservice
 * @description
 * # Myservice
 * Service in the postWallApp.
 */
angular.module('postWallApp')
    .factory('Myservice', ['$http', function($http) {
    var urlBase = 'service.php';
    var Service = {};

    Service.who = function() {
        return $http({
            method: 'GET',
            url: urlBase + '?w=1', 
        });
    }
    Service.query = function () {
        return $http({
            method: 'GET',
            url: urlBase + '?q=1', 
        });
    };
    Service.add = function(content, color) {
        return $http({
            method: 'POST',
            url: urlBase,
            data: {
                q: 'a',
                raw: content,
                color: color
            }
        });
    };
    Service.remove = function(post) {
        return $http({
            method: 'POST',
            url: urlBase,
            data: {
                q: 'd',
                id: post.rowid
            }
        });
    };
    Service.addComment = function(post, comment) {
        return $http({
            method: 'POST',
            url: urlBase,
            data: {
                q: 'ca',
                id: post.rowid,
                c: comment
            }
        });
    };
    Service.removeComment = function(comment) {
        return $http({
            method: 'POST',
            url: urlBase,
            data: {
                q: 'cd',
                id: comment.rowid
            }
        });
    };
    return Service;
}]);
