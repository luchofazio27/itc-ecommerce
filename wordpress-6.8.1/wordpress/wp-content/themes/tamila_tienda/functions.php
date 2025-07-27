<?php
if(!defined('ABSPATH')) die();
if(!function_exists('tamila_tienda_setup')){
 function tamila_tienda_setup(){
    //imágenes destacadas
    add_theme_support( 'post-thumbnails' );
    if ( ! current_user_can( 'manage_options' ) ) {
        show_admin_bar( false );
    }
 }   
 add_action('after_setup_theme', 'tamila_tienda_setup');
}
##menú
add_action('init', function(){
    register_nav_menus([
        'menu-principal'=>__('Menú Principal', 'tamila_tienda')
    ]);
});
#enqueue
if(!function_exists('tamila_tienda_scripts_styles')){
    function tamila_tienda_scripts_styles(){

        wp_enqueue_script( "sweetalert2",    get_template_directory_uri().'/assets/js/sweetalert2.js' , array('jquery')); 
       
     }
     add_action('wp_enqueue_scripts', 'tamila_tienda_scripts_styles');
}
