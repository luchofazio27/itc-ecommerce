<?php

/**
 * Este archivo configura algunas funciones básicas del tema.
 */
if (!defined('ABSPATH')) die();
if (!function_exists('itc_ecommerce_setup')) {
    function itc_ecommerce_setup()
    {
        // Agregar soporte para características del tema
        add_theme_support('post-thumbnails'); // Soporte para miniaturas de publicaciones
    }
    add_action('after_setup_theme', 'itc_ecommerce_setup');
}
##menu
add_action('init', function () {
    register_nav_menus([
        'menu-principal' => __('Menú Principal', 'itc_ecommerce')
    ]);
});
#enqueue
if (!function_exists('itc_ecommerce_scripts_styles')) {
    function itc_ecommerce_scripts_styles()
    {
        wp_enqueue_script("sweetalert2", get_template_directory_uri() .'/assets/js/sweetalert2.js', array('jquery'));
    }
    add_action('wp_enqueue_scripts', 'itc_ecommerce_scripts_styles');
}
