<?php 
if(!defined('ABSPATH')) die(); // Evita acceso directo al archivo

// JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Define el shortcode [itc_tienda_reset id="1"]
if(!function_exists('itc_tienda_reset_codigo_corto')){
    add_action('init', 'itc_tienda_reset_codigo_corto');
    function itc_tienda_reset_codigo_corto(){
        add_shortcode('itc_tienda_reset', 'itc_tienda_reset_codigo_corto_display');
    }
}

// Procesa el POST del formulario de restablecimiento de contraseña
if(!function_exists('itc_tienda_reset_post')){ // Define la función para procesar el formulario
    function itc_tienda_reset_post(){ // Verifica si se envió el formulario
        if(isset($_POST['nonce']) and $_POST['action'] == 'reset-in' ){ // Verifica el nonce y la acción
            if(!isset($_GET['t'])){ // Verifica si el token JWT está presente
                wp_safe_redirect(home_url('error')); exit; // Redirige a error si no hay token
            }

            global $wpdb; // Accede a la base de datos de WordPress
            // Obtiene la clave secreta JWT desde la base de datos
            $datos = $wpdb->get_results("SELECT nombre, valor FROM {$wpdb->prefix}itc_tienda_variables_globales WHERE id IN(5);", ARRAY_A);

            require 'vendor/autoload.php'; // Carga las dependencias de JWT

            try {
                // Decodifica el token JWT
                $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512'));
                
                // Establece nueva contraseña al usuario con ID en el campo 'aud'
                wp_set_password(sanitize_text_field($_POST['password']), $decode->aud);

                // Redirige con mensaje de éxito
                wp_safe_redirect(home_url('reset') . "?error=3&t=" . $_GET['t']); exit;
            } catch (\Throwable $th) {
                // Si falla el token, redirige con mensaje de error
                wp_safe_redirect(home_url('reset') . "?error=2&t=" . $_GET['t']); exit;
            }
        }
    }
}

// Ejecuta el procesamiento del formulario después de cargar el tema
add_action('after_setup_theme', 'itc_tienda_reset_post');

// Muestra el formulario de restablecer contraseña
if(!function_exists('itc_tienda_reset_codigo_corto_display')){
    function itc_tienda_reset_codigo_corto_display($argumentos, $content=""){ // Verifica si el token JWT está presente
        if(!isset($_GET['t'])){ // Si no hay token, redirige a error
            wp_safe_redirect(home_url('error')); exit; // Redirige a error si no hay token
        }

        global $wpdb;
        // Obtiene la clave JWT
        $datos = $wpdb->get_results("SELECT nombre, valor FROM {$wpdb->prefix}itc_tienda_variables_globales WHERE id IN(5);", ARRAY_A);

        require 'vendor/autoload.php';

        try {
            // Decodifica el token
            $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512')); // Si el token es válido, muestra el formulario

            $html = ''; // Inicia el HTML del formulario
            $html .= '<div class="container"><form action="'.get_site_url().'/reset/?t='.$_GET['t'].'" method="POST" name="itc_tienda_reset_form">'; // Inicia el formulario HTML
            $html .= '<div class="row">'; // Inicia la fila del formulario

            // Muestra alerta de error
            if(isset($_REQUEST["error"]) && sanitize_text_field($_REQUEST['error']) == '2'){
                $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Ups</strong> Ocurrió un error inesperado, por favor vuelve a intentarlo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            }

            // Muestra alerta de éxito
            if(isset($_REQUEST["error"]) && sanitize_text_field($_REQUEST['error']) == '3'){
                $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Excelente</strong> Has cambiado tu contraseña exitosamente, ahora loguéate y aprovecha todos nuestros descuentos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            }

            $html .= '<div class="col-8">';
            $html .= '<h2>Restablecer mi contraseña</h2><p>Necesitamos que te crees una nueva contraseña</p><hr/>';

            // Campo de nueva contraseña
            $html .= '<div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
            </div>';

            // Campo repetir contraseña
            $html .= '<div class="mb-3">
                <label for="password2" class="form-label">Repetir Contraseña:</label>
                <input type="password" name="password2" id="password2" class="form-control" placeholder="Repetir Contraseña" /> 
            </div>';

            // Campos ocultos de seguridad y acción
            $html .= '<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" />';
            $html .= '<input type="hidden" name="action" value="reset-in" />';
            $html.='<input type="hidden" name="return" value="'.( isset($_REQUEST['return']) ? sanitize_text_field($_REQUEST['return']) : '' ).'" />';


            // Botón de envío
            $html .= '<hr />';
            $html .= '<a href="javascript:void(0);" class="btn btn-warning" onclick="itc_tienda_reset()" title="Enviar"><i class="fas fa-lock"></i> Enviar</a> ';
            $html .= '</div></div></form>';

            // Links adicionales
            $html .= '<hr/><p><a href="'.get_site_url().'/login" title="Ya tengo cuenta">Ya tengo cuenta</a> | <a href="'.get_site_url().'/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
            $html .= '</div>';

            return $html;
        } catch (\Throwable $th) {
            // Si el token es inválido, redirige a la página de error
            echo '<script>window.location="'.get_site_url().'/error";</script>'; exit;
        }
    }
}
