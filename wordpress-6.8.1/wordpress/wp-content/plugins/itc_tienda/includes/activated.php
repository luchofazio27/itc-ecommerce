<?php 
if(!defined('ABSPATH')) die(); // Seguridad: evita acceso directo al archivo

// Se importan las clases necesarias para trabajar con JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Este shortcode será algo como: [itc_tienda_activated]
if(!function_exists('itc_tienda_activar_codigo_corto')) {
    add_action('init', 'itc_tienda_activar_codigo_corto'); // Registra el shortcode al inicializar WordPress
    function itc_tienda_activar_codigo_corto() {
        add_shortcode('itc_tienda_activated', 'itc_tienda_activar_codigo_corto_display'); // Define el shortcode y su función asociada
    }
}

if(!function_exists('itc_tienda_activar_codigo_corto_display')) {
    function itc_tienda_activar_codigo_corto_display($argumentos, $content="") {
        if(!isset($_GET['t'])) { // Si no se pasa un token por GET
            echo '<script>window.location="'.get_site_url().'/error";</script>'; exit; // Redirige al error
        }

        global $wpdb;
        // Obtiene la clave secreta del JWT desde la base de datos
        $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}itc_tienda_variables_globales where id in(5);", ARRAY_A);

        require 'vendor/autoload.php'; // Carga las dependencias necesarias (incluye JWT)

        try {
            // Decodifica el token JWT recibido por GET con la clave secreta
            $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512'));

            // Verifica si el usuario ya está marcado como verificado
            if(get_user_meta($decode->aud, 'itc_tienda_verificacion', true)['verificacion']=='1') {
                echo '<script>window.location="'.get_site_url().'/error";</script>'; exit; // Si ya estaba verificado, error
            } else {
                // Marca al usuario como verificado en su meta (clave personalizada)
                update_user_meta($decode->aud, 'itc_tienda_verificacion', ['verificacion'=>'1']);
                // Redirige al login con código de éxito (?error=4 lo estás usando como confirmación)
                echo '<script>window.location="'.get_site_url().'/login?error=4";</script>'; exit;
            }

        } catch (\Throwable $th) {
            // Si falla la decodificación del token, redirige a error
            echo '<script>window.location="'.get_site_url().'/error";</script>'; exit;
        }
    }
}
