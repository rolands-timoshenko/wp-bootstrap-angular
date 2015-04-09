<?php

include "inc/class-wprt-walker.php";

/**
 * Load theme styles & scripts
 */
function wprt_theme_styles_scripts(){ 
    /**
     * Main theme css fails. For main markup style use bootstrap.
     */
    wp_register_style('bootstrap',  get_template_directory_uri().'/css/bootstrap.css'       );
    wp_register_style('theme',      get_template_directory_uri().'/css/theme.css'           );
    
    wp_register_script('jquery',   get_template_directory_uri().'/js/jquery-1.11.2.min.js','','',true);
    
    /**
     * Angular.
     */
    wp_register_script('angular',   get_template_directory_uri().'/js/angular.min.js','','',true);
    wp_register_script('angular-bootstrap',   get_template_directory_uri().'/js/ui-bootstrap-0.12.1.min.js','','',true);
    wp_register_script('angular-route',   get_template_directory_uri().'/js/angular-route.min.js','','',true);
    wp_register_script('angular-sanitize',   get_template_directory_uri().'/js/angular-sanitize.min.js','','',true);
    wp_register_script('angular-app',   get_template_directory_uri().'/app/app.js','','',true);
    wp_register_script('angular-app-routes',   get_template_directory_uri().'/app/routes.js','','',true);
    wp_register_script('angular-root-controller',   get_template_directory_uri().'/app/controllers/root.controller.js','','',true);
    wp_register_script('angular-posts-controller',   get_template_directory_uri().'/app/controllers/posts.controller.js','','',true);
    wp_register_script('angular-post-controller',   get_template_directory_uri().'/app/controllers/post.controller.js','','',true);
    
    wp_register_script('angular-app-derective-infinity',   get_template_directory_uri().'/app/directives/infinityScroll.js','','',true);
    
    wp_enqueue_style('bootstrap'        );
    wp_enqueue_style('theme'            );
    wp_enqueue_script('jquery'         );
    wp_enqueue_script('angular'         );
    wp_enqueue_script('angular-bootstrap'         );
    wp_enqueue_script('angular-route'   );
    wp_enqueue_script('angular-sanitize'   );
    wp_enqueue_script('angular-app'     );
    wp_enqueue_script('angular-app-routes'     );
    wp_enqueue_script('angular-root-controller'     );
    wp_enqueue_script('angular-posts-controller'     );
    wp_enqueue_script('angular-post-controller'     );
    
    wp_enqueue_script('angular-app-derective-infinity'     );

    
    /**
     * JS global vars.
     */
    wp_localize_script(
        'angular-app',
        'app',
        array(
            'views' => trailingslashit( get_template_directory_uri() ) . 'app/views/'
        )
    );
    
    
}add_action('wp_enqueue_scripts', 'wprt_theme_styles_scripts');

/**
 * Load menus
 */
function register_my_menu() {
  register_nav_menu('header-menu',__( 'Header Menu' ));
}add_action( 'init', 'register_my_menu' );

?>