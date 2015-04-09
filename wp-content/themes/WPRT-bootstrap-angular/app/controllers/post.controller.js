wp.controller('PostController', function ($scope,$http,$routeParams,$sce) {
    
    var postID          = $routeParams.id;
    
    $scope.post         = {};
    $scope.comments     = [];
    $scope.viewPath     = app.views;
    $scope.commentForm  = {comment_parent:''};
    
    function __loadPost(callback){
        $http.get('wp-json/posts/'+postID).success(function(post){
            $scope.post = post;
            if(callback)callback();
        });
    }
    
    function __loadReplies(args,callback){
        
        var number = (number in args)? 0 : args.number;
        var offset = (offset in args)? 0 : args.offset;
        var parent = (parent in args)? 0 : args.parent;
        
        $http.get('wp-json/posts/'+postID+"/comments/?number="+number+"&offset="+offset+"&parent="+parent).success(function(replies){
            if(callback)callback(replies);
        });
    }
    
    function __loadComments(args,callback){

        var number = (number in args)? 0 : args.number;
        var offset = (offset in args)? 0 : args.offset;
        var parent = (parent in args)? 0 : args.parent;
        
        $http.get('wp-json/posts/'+postID+"/comments/?number="+number+"&offset="+offset+"&parent="+parent).success(function(comments){
            if(comments.length<1)return;
            for(var key in comments){
                $scope.comments.push(comments[key]);  
            }
            if(callback)callback();
        });
    }
    
    function __addComment(data,callback){
        $http.post('wp-json/posts/'+postID+"/comments/add",data).success(function(post){
            if(callback)callback(post);
        });
    }
    
    function __resetFormFields(fields,resetTo){
        for(var key in fields){
            fields[key] = resetTo;
        }
    };
    
    $scope.addComment = function(comment){
        var postData = {};
        postData.comment_author_email   = $scope.commentForm.comment_author;
        postData.comment_author         = $scope.commentForm.comment_author;
        postData.comment_content        = $scope.commentForm.comment_content;
        postData.parent                 = $scope.commentForm.comment_parent;
        __addComment(postData,function(data){
            if(!data){
                console.error("data Empty");
                return;
            }
            if(comment){
                if(!comment.comments)comment.comments = [];
                comment.comments.unshift(data);
            }else{
                $scope.comments.unshift(data);
            }
            __resetFormFields($scope.commentForm,'')
        });
    };

    $scope.loadComments = function(){
        $scope.loading=true;
        __loadComments({number:4,offset:($scope.comments.length),parent:0},function(){
            $scope.loading=false;
        });
    };
    
    $scope.loadReplies = function(commentID,comment){
        __loadReplies({parent:commentID},function(data){
            comment.comments = data;
        });
    };
    
    $scope.setCommentParent = function(commentID){
        $scope.commentForm.comment_parent = commentID;
    };
    
    $scope.html = function (string) {
        return $sce.trustAsHtml(string);
    };
    
    __loadPost();
    __loadComments({number:4,offset:0,parent:0});
});


