<?php get_header(); ?>
<div class="jumbotron">
    <div class="container">
        <h1>Hello, world!</h1>
        <p>{{jumbotron_text}}</p>
        <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a></p>
    </div>
</div>
<div class="container" ng-view=""></div>
<?php get_footer(); ?>
