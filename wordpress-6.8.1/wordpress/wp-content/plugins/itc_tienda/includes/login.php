<?php
if (!defined('ABSPATH')) die(); // Evita acceso directo al archivo

// [itc_tienda_login] - Registro del shortcode
add_action('init', function () {
    add_shortcode('itc_tienda_login', 'itc_tienda_login_codigo_corto_display'); // Define el shortcode y su función asociada
});

add_action('after_setup_theme', function () {
    // Comprueba si se envió el formulario de login
    if (isset($_POST['nonce']) and $_POST['action'] == 'log-in') {
        // Intenta iniciar sesión con los datos enviados
        $login = wp_signon(array( // wp_signon inicia sesión con los datos del usuario
            'user_login'    => sanitize_text_field($_POST['correo']), // Sanitiza el correo
            'user_password' => sanitize_text_field($_POST['password']), // Sanitiza la contraseña
            'remember'      => false // No recordar sesión
        ), false);

        if ($login->ID) { // Si el login es correcto
            wp_clear_auth_cookie(); // Limpia cookies previas
            do_action('wp_login', $login->ID); // Dispara acción de login
            wp_set_current_user($login->ID); // Establece el usuario actual
            wp_set_auth_cookie($login->ID, true); // Crea la cookie de sesión

            if (empty($_POST['return'])) { // Si no hay URL de retorno
                echo '<script>window.location="' . get_site_url() . '/perfil";</script>';
                exit; // Redirige a perfil
            } else { // Si hay URL de retorno
                echo '<script>window.location="' . get_site_url() . '/tienda/' . base64_decode($_POST['return']) . '";</script>';
                exit; // Redirige a la página deseada
            }
        } else {
            wp_safe_redirect(home_url('login') . "?error=1");
            exit; // Si error, redirige a login con error
        }
    }
});

// Función del shortcode [itc_tienda_login]
if (!function_exists('itc_tienda_login_codigo_corto_display')) {
    function itc_tienda_login_codigo_corto_display($argumentos, $content = "")
    {
        if (is_user_logged_in()) { // Si ya está logueado
            echo '<script>window.location="' . get_site_url() . '";</script>';
            exit; // Redirige a home
        }

        $html = ''; // Contenedor del HTML

        // Inicio del formulario
        $html .= '<div class="container"><form action="' . get_site_url() . '/login" method="POST" name="itc_tienda_login_form">';
        $html .= '<div class="row">';

        // Muestra mensaje si hay error 1 (credenciales inválidas)
        if (isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '1') {
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> Las credenciales ingresadas son inválidas.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }

        // Muestra mensaje si hay error 4 (cuenta activada correctamente)
        if (isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '4') {
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente!!!</strong> Tu cuenta ha sido activada correctamente. Ahora puedes loguearte y disfrutar de nuestros fabulosos descuentos!!!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }

        $html .= '<div class="col-8">';
        $html .= '<h2>Login</h2>';

        // Campo de correo
        $html .= '<div class="mb-3">
              <label for="correo" class="form-label">E-Mail:</label>
              <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="' . esc_attr($_POST['correo'] ?? '') . '" />
              </div>';


        // Campo de contraseña
        $html .= '<div class="mb-3">
               <label for="password" class="form-label">Contraseña:</label>
               <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
              </div>';

        // Campos ocultos para seguridad y control
        $html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce('seg') . '" id="nonce" /><input type="hidden" name="action" value="log-in" />';
        $html .= '<input type="hidden" name="return" value="' . sanitize_text_field($_REQUEST['return'] ?? '') . '" />';

        // Botón de envío
        $html .= '<hr />';
        $html .= '<a href="javascript:void(0);" class="btn btn-warning" onclick="itc_tienda_login()" title="Entrar"><i class="fas fa-user-lock"></i> Entrar</a> ';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';

        // Enlaces de recuperación de contraseña y registro
        $html .= '<hr/><p><a href="' . get_site_url() . '/restablecer" title="Restablecer mi contraseña">Restablecer mi contraseña</a> | <a href="' . get_site_url() . '/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
        $html .= '</div>';

        return $html; // Devuelve el HTML generado
    }
}

// Control de acceso a páginas restringidas

add_action('after_setup_theme', function () {
    $url      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // Obtiene la URL actual
    $url_path = parse_url($url, PHP_URL_PATH); // Extrae el path de la URL
    $slug     = pathinfo($url_path, PATHINFO_BASENAME); // Obtiene el último segmento de la URL

    // Si está en checkout, perfil o verificación
    if ($slug == 'checkout' or $slug == 'perfil' or $slug == 'verificacion') {
        if (!is_user_logged_in()) { // Si no está logueado
            echo '<script>window.location="' . get_site_url() . '/login";</script>';
            exit; // Redirige a login
        } else {
    if ($slug == 'checkout' or $slug == 'verificacion') {
        $userdata = wp_get_current_user(); // ✅ Obtiene el usuario logueado
        $verificacion = get_user_meta($userdata->ID, 'itc_tienda_verificacion', true);

        if (isset($verificacion['verificacion']) && $verificacion['verificacion'] == '0') {
            echo '<script>window.location="' . get_site_url() . '/perfil?error=3";</script>';
            exit; // Redirige a perfil con error
        }
    }
}

    }
});
