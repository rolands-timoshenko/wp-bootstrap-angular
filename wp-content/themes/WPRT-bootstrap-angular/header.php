<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri();?>/ico/favicon.png">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> ng-app="wp">
    <header>
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" ng-init="navbarCollapsed=true" ng-click="navbarCollapsed = !navbarCollapsed">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo site_url(); ?>"><?php echo get_bloginfo( "title" ); ?></a>
                </div>
                <div class="collapse navbar-collapse" collapse="navbarCollapsed">
                <?php wp_nav_menu(array( 
                    'theme_location'    => 'header-menu',
                    'menu_class'        => 'nav navbar-nav navbar-right',
                    'walker'            => new WPRT_Walker(),
                ));?>
                </div>
            </div>
        </nav>
    </header>