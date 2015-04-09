<form class="row" action="<?php bloginfo('siteurl'); ?>" id="searchform" method="get">
    <div class="input-group col-md-12">
        <input type="text" ng-model="jumbotron_text" placeholder="Search for..." name="s" class="form-control">
        <span class="input-group-btn">
            <button type="submit" class="btn btn-info">
                <i class="glyphicon glyphicon-search"></i>
            </button>
        </span>
    </div>
</form>