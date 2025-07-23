<?php
/*
Plugin Name: Tamila Tienda
Plugin URI: https://www.cesarcancino.com/
Description: Este plugin es para crear tienda virtual
Version: 1.0.1
Author: César Cancino
Author URI: https://www.cesarcancino.com/
License: GPL
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tamila_tienda 
*/
require_once plugin_dir_path( __FILE__ ) . 'includes/init.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/bloquear.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/slide.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/paginas.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/productos.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/productos_galeria.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/usuarios.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/registro.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/activated.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/restablecer.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/reset.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/login.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/perfil.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/comprar.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/checkout.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/verificacion.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ventas.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/excel.php';
if(!defined('ABSPATH')) die();
if(!function_exists('tamila_tienda_instalar')){
    function tamila_tienda_instalar(){
        tamila_tienda_init();
        tamila_tienda_slide_init();
        //crear páginas
        tamila_tienda_crear_paginas();
        //crear tablas para productos galería
        tamila_tienda_productos_galeria_init();
    }
}
if(!function_exists('tamila_tienda_desactivar')){
    function tamila_tienda_desactivar(){
        //eliminar páginas
        tamila_tienda_eliminar_paginas();
        #limpiador de enlaces permanentes
        flush_rewrite_rules( );
    }
}
#activar plugins
register_activation_hook( __FILE__, 'tamila_tienda_instalar' );
#desactivar
register_deactivation_hook( __FILE__, 'tamila_tienda_desactivar' );

if(!function_exists('tamila_tienda_scripts')){
    function tamila_tienda_scripts($hook){
         
        if($hook=='tamila_tienda/includes/admin/listar.php' or $hook=='tamila_tienda/includes/admin/editar.php' or $hook=='tamila_tienda/includes/admin/slide_listar.php' or $hook=='tamila_tienda/includes/admin/pasarelas.php' or $hook=='tamila_tienda/includes/admin/variables_globales.php' or $hook=='tamila_tienda/includes/admin/ventas.php'){
            wp_enqueue_style( "bootstrapcss",  plugins_url( 'assets/css/bootstrap.min.css', __FILE__ ) );
        //https://cdnjs.com/libraries/font-awesome
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        
        
        wp_enqueue_style( "sweetalert2",  plugins_url( 'assets/css/sweetalert2.css', __FILE__ ) );
        wp_enqueue_script( "sweetalert2js",  plugins_url( 'assets/js/sweetalert2.js', __FILE__ ) );  
        wp_enqueue_script( "popper",  plugins_url( 'assets/js/popper.min.js.js', __FILE__ ), array( ));
        wp_enqueue_script( "bootstrapjs",  plugins_url( 'assets/js/bootstrap.min.js', __FILE__ ), array('jquery')); 
        wp_enqueue_script( "funcionesj",  plugins_url( 'assets/js/funciones.js', __FILE__ ) );
        wp_localize_script('funcionesj','datosajax',[
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('seg')
        ]);
        //llamamos a lo necesario para manejar media
        wp_enqueue_media();
        }else{
            return;
        }
        
       
     }
     add_action('admin_enqueue_scripts', 'tamila_tienda_scripts'); 
}
