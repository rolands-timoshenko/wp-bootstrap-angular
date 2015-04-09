wp.controller('PostsController', function ($scope,$http,$routeParams,$sce) {
    
    /**
     * Posts Page settings
     */
    var postsPerPage = 8;
    var currentPage = 1;
    var nothingToLoad = false;
    
    $scope.posts = [];
   
    /**
     * Load Posts. Send GET request to api.
     * @returns {Boolean}
     */
    function __LoadPosts(){
        $scope.busy=true;
        currentPage=Math.ceil(($scope.posts.length/postsPerPage)+1);
        $http.get('wp-json/posts/?filter[posts_per_page]='+postsPerPage+'&page='+currentPage).success(function(posts){
            for(var key in posts){
                $scope.posts.push(posts[key]);  
            }
            if(posts.length < 1)nothingToLoad=true;
            $scope.busy=false;
        });
        return true;
    }__LoadPosts();
    
    
    /**
     * Calls when user scroll bottom.
     * Load next page content.
     * @returns {Boolean}
     */
    $scope.nextPage = function(){
        if(nothingToLoad)return false;
        __LoadPosts();
        return true;
    };
    
    $scope.html = function (string) {
        return $sce.trustAsHtml(string);
    };
});


