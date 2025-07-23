<?php 
if(!defined('ABSPATH')) die(); 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
//[tamila_tienda_login id="1"]
if(!function_exists('tamila_tienda_activar_codigo_corto')){
    add_action('init', 'tamila_tienda_activar_codigo_corto');
    function tamila_tienda_activar_codigo_corto(){
        add_shortcode( 'tamila_tienda_activated', 'tamila_tienda_activar_codigo_corto_display' );
    }
}
if(!function_exists('tamila_tienda_activar_codigo_corto_display')){
    function tamila_tienda_activar_codigo_corto_display($argumentos, $content=""){
        if(!isset($_GET['t'])){
            echo '<script>window.location="'.get_site_url().'/error";</script>';exit;
        }
        global $wpdb;
        $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}tamila_tienda_variables_globales where id in(5);", ARRAY_A);
        require 'vendor/autoload.php';
        try {
            $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512'));
            if(get_user_meta($decode->aud, 'tamila_tienda_verificacion', true)['verificacion']=='1'){
                echo '<script>window.location="'.get_site_url().'/error";</script>';exit;
            }else{
                update_user_meta( $decode->aud, 'tamila_tienda_verificacion', ['verificacion'=>'1'] );
                echo '<script>window.location="'.get_site_url().'/login?error=4";</script>';exit;
            }
        } catch (\Throwable $th) {
            echo '<script>window.location="'.get_site_url().'/error";</script>';exit;
        }
    }
}