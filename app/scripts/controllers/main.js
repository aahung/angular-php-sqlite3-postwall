'use strict';

/**
 * @ngdoc function
 * @name postWallApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the postWallApp
 */
angular.module('postWallApp')
  .controller('MainCtrl', function ($scope, Myservice) {
    $scope.user = 'log in';
    Myservice.who().success(function(who) {
        if (who == '') {
            window.location.href = 
                '//ourphysics.org/wiki/index.php/Special:AuthApi?jump='
                + "//ourphysics.org/apps/postwall/auth.php";
        };
        $scope.user = who;
    }).error(function(data) {alert(data)});
    $scope.addPost = function(content, color) {
        $scope.newContent = '';
        Myservice.add(content, color).success(function(posts) {
            $scope.posts = posts;
            $scope.startMasonry();
        }).error(function(data) {alert(data)});
    };
    $scope.removePost = function(post) {
        if (!confirm('Are you sure you are going to delete this post?')) return;
        Myservice.remove(post).success(function(posts) {
            $scope.posts = posts;
            $scope.startMasonry();
        }).error(function(data) {alert(data)});
    };
    $scope.editPost = function(post) {
        if (!confirm('Are you sure you are going to edit this? Time of this post will be reset to now.')) return;
        $scope.newContent = post.content;
        window.scrollTo(0, 0); // jump to top

        Myservice.remove(post).success(function(posts) {
            $scope.posts = posts;
            $scope.startMasonry();
        }).error(function(data) {alert(data)});
    };
    $scope.addComment = function(post, newComment) {
        Myservice.addComment(post, newComment).success(function(posts) {
            $scope.posts = posts;
            $scope.startMasonry();
        }).error(function(data) {alert(data)});
    };
    Myservice.query().success(function(posts) {
        $scope.posts = posts;
        $scope.startMasonry();
    }).error(function(data) {alert(data)});
    $scope.startMasonry = function() {
        window.setTimeout(function() {
            var msnry;
            var container = document.querySelector('.post-wall');
            imagesLoaded(container, function() {
                msnry = new Masonry(container, {
                  // options
                    columnWidth: 360,
                    itemSelector: '.item'
                });
            });
        }, 100);
    }
    $scope.startMasonry();
  });
