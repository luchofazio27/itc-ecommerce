<?php 
if(!defined('ABSPATH')) die(); // Seguridad: evita ejecución directa del archivo

// Importa clases necesarias de JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Incluye utilidades personalizadas del plugin
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';

// Shortcode: [itc_tienda_restablecer id="1"]
if(!function_exists('itc_tienda_restablecer_codigo_corto')){
    add_action('init', 'itc_tienda_restablecer_codigo_corto'); // Registra el shortcode
    function itc_tienda_restablecer_codigo_corto(){
        add_shortcode('itc_tienda_restablecer', 'itc_tienda_restablecer_codigo_corto_display'); // Asocia el shortcode con la función de renderizado
    }
}

// Acción POST del formulario de restablecimiento
if(!function_exists('itc_tienda_restablecer_post')){
    function itc_tienda_restablecer_post(){
        // Verifica si el formulario fue enviado correctamente
        if(isset($_POST['nonce']) and $_POST['action'] == 'restablecer-in'){ 
            $existe = email_exists(sanitize_text_field($_POST['correo'])); // Verifica si el correo existe en WP

            if($existe === false){
                wp_safe_redirect(home_url('restablecer')."?error=1"); exit; // Redirige con error si no existe el correo
            }

            // Prepara datos para el token JWT
            $payload = [
                'iss' => get_site_url(),         // Emisor
                'aud' => $existe,                // ID del usuario
                'iat' => time(),                 // Fecha de emisión
                'exp' => time() + 60             // Expira en 60 segundos (ojo, antes decía * 60, lo corregí)
            ];

            // Genera token y URL personalizada para restablecimiento
            $jwt = itc_tienda_generate_jwt($payload);
            $url = get_site_url() . "/reset?t=" . $jwt;

            // Cuerpo del mensaje
            $mensaje = '<h1>Restablecer tu contraseña en '.get_bloginfo('name').'</h1> 
            Hola, has solicitado restablecer tu contraseña. Abre esta URL: <br/>'.$url.'<br/> 
            o copia y pégala en tu navegador.<br/>
            <a href="'.$url.'">'.$url.'</a>
            <hr />';

            // Envía el correo
            itc_tienda_envia_correo(sanitize_text_field($_POST['correo']), "Restablecer contraseña", $mensaje);

            // Redirige con mensaje de éxito
            wp_safe_redirect(home_url('restablecer')."?error=2"); exit;
        }
    }
}
// Ejecuta la función anterior al cargar el tema (después de la inicialización del theme)
add_action('after_setup_theme', 'itc_tienda_restablecer_post');

// Renderiza el formulario de restablecimiento
if(!function_exists('itc_tienda_restablecer_codigo_corto_display')){
    function itc_tienda_restablecer_codigo_corto_display($argumentos, $content=""){
        $html = '';

        $html .= '<div class="container restablecer-container"><form action="'.get_site_url().'/restablecer" method="POST" name="itc_tienda_restablecer_form">';
        $html .= '<div class="row justify-content-center">';

        // Mensaje si el email no existe
        if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '1'){
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> El E-Mail ingresado no es válido.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }

        // Mensaje de éxito si el email fue enviado
        if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '2'){
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente!!!</strong> Te hemos enviado un correo a la dirección de E-Mail que nos indicaste con las instrucciones a seguir.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }

        $html .= '<div class="col-lg-8 col-md-10">';
        $html .= '<div class="form-card">';
        $html .= '<h2 class="form-title">Restablecer mi contraseña</h2>
                  <p class="form-text">Indícanos tu correo electrónico para enviarte las instrucciones para que puedas restablecer tu contraseña</p>';

        // Input de correo
        $html .= '<div class="mb-3">
        <label for="correo" class="form-label">E-Mail:</label>
        <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="'.(isset($_POST['correo']) ? esc_attr($_POST['correo']) : '').'" /> 
        </div>';

        // Campos ocultos: nonce, action y return
        $html .= '<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" />';
        $html .= '<input type="hidden" name="action" value="restablecer-in" />';
        $html .= '<input type="hidden" name="return" value="'.(isset($_REQUEST['return']) ? esc_attr($_REQUEST['return']) : '').'" />';
        $html .= '<hr />';

        // Botón de envío
        $html .= '<a href="javascript:void(0);" class="btn btn-warning w-100" onclick="itc_tienda_restablecer()" title="Enviar">
        <i class="fas fa-envelope"></i> Enviar</a>';

        $html .= '</div>'; // cierra form-card
        $html .= '</div>'; // cierra col
        $html .= '</div>'; // cierra row
        $html .= '</form>';

        // Enlaces de acceso
        $html .= '<hr/><p class="text-center"><a href="'.get_site_url().'/login" title="Ya tengo cuenta">Ya tengo cuenta</a> | 
        <a href="'.get_site_url().'/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';

        $html .= '</div>'; // cierra container

        // Estilos embebidos
        $html .= '<style>
        .restablecer-container {
            padding: 40px 15px;
            max-width: 100%;
        }
        .form-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }
        .form-text {
            font-size: 1rem;
            margin-bottom: 20px;
            color: #555;
        }
        .form-label {
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }
            .form-title {
                font-size: 1.4rem;
            }
            .form-text {
                font-size: 0.9rem;
            }
        }
        </style>';

        return $html;
    }
}
