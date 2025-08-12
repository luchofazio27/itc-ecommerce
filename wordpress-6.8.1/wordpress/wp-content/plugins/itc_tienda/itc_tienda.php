<?php
/*
Plugin Name: ITC Tienda
Plugin URI: https://www.liffdomotic.com/
Description: Este plugin es para crear tienda virtual
Version: 1.0.1
Author: Liff Domotic
Author URI: https://www.liffdomotic.com/
License: GPL
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: itc_tienda
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/init.php';
//require_once plugin_dir_path( __FILE__ ) . 'includes/bloquear.php';
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

if (!defined('ABSPATH')) die();
if(!function_exists('itc_tienda_instalar')){
    function itc_tienda_instalar(){
        itc_tienda_init();
        itc_tienda_slide_init();
        //crear páginas
        itc_tienda_crear_paginas();
        //crear tablas para productos galería
        itc_tienda_productos_galeria_init();
    }
}
if(!function_exists('itc_tienda_desactivar')){
    function itc_tienda_desactivar(){
        //eliminar páginas
        itc_tienda_eliminar_paginas();
        #limpiador de enlaces permanentes
        flush_rewrite_rules( );
    }
}
#activar plugins
register_activation_hook( __FILE__, 'itc_tienda_instalar' );
#desactivar
register_deactivation_hook( __FILE__, 'itc_tienda_desactivar' );


if(!function_exists('itc_tienda_scripts')){
    function itc_tienda_scripts($hook){
         
        if($hook=='itc_tienda/includes/admin/listar.php' or $hook=='itc_tienda/includes/admin/editar.php' or $hook=='itc_tienda/includes/admin/slide_listar.php' or $hook=='itc_tienda/includes/admin/pasarelas.php' or $hook=='itc_tienda/includes/admin/variables_globales.php' or $hook=='itc_tienda/includes/admin/ventas.php'){
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
     add_action('admin_enqueue_scripts', 'itc_tienda_scripts'); 
}