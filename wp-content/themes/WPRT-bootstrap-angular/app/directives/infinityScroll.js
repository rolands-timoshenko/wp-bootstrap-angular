wp.directive("infinity", function(){
    return {
        restrict: 'A',
        scope: true,
        link: function (scope, element, attrs) {
            jQuery(window).scroll(function() {
                if(jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height()) {
                    var busy = scope.$apply(attrs.infinityBusy);
                    if(!busy)scope.$apply(attrs.infinityBottom);
                }
            });
        }
    };
});