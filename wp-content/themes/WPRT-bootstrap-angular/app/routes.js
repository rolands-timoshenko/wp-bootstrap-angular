wp.config(function($routeProvider) {
    $routeProvider.
    when('/', {
        templateUrl: app.views+'index.html',
        controller: 'RootController'
    }).
    when('/posts/', {
        templateUrl: app.views+'posts.html',
        controller: 'PostsController'
    }).
    when('/posts/:id', {
        templateUrl: app.views+'post.html',
        controller: 'PostController'
    }).
    otherwise({
        redirectTo: '/'
    });
});