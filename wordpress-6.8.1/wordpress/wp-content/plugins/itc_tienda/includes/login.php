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
        $login = wp_signon(array(
            'user_login'    => sanitize_text_field($_POST['correo']), // Sanitiza el correo
            'user_password' => sanitize_text_field($_POST['password']), // Sanitiza la contraseña
            'remember'      => false
        ), false);

        if ($login->ID) { // Si el login es correcto
            wp_clear_auth_cookie();
            do_action('wp_login', $login->ID);
            wp_set_current_user($login->ID);
            wp_set_auth_cookie($login->ID, true);

            if (empty($_POST['return'])) {
                echo '<script>window.location="' . get_site_url() . '/perfil";</script>';
                exit;
            } else {
                echo '<script>window.location="' . get_site_url() . '/tienda/' . base64_decode($_POST['return']) . '";</script>';
                exit;
            }
        } else {
            wp_safe_redirect(home_url('login') . "?error=1");
            exit;
        }
    }
});

// Función del shortcode [itc_tienda_login]
if (!function_exists('itc_tienda_login_codigo_corto_display')) {
    function itc_tienda_login_codigo_corto_display($argumentos, $content = "")
    {
        if (is_user_logged_in()) {
            echo '<script>window.location="' . get_site_url() . '";</script>';
            exit;
        }

        $html = '';

        // ✅ Estilos CSS para centrar y hacerlo responsive
        $html .= '<style>
            .login-container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .login-container h2 {
                text-align: center;
                margin-bottom: 20px;
                font-weight: bold;
            }
            .login-container .btn-warning {
                width: 100%;
                font-weight: bold;
            }
            .login-container p {
                text-align: center;
                margin-top: 15px;
            }
            /* Responsive */
            @media (max-width: 768px) {
                .login-container {
                    width: 95%;
                    padding: 15px;
                }
            }
        </style>';

        // Inicio del formulario
        $html .= '<div class="login-container"><form action="' . get_site_url() . '/login" method="POST" name="itc_tienda_login_form">';
        $html .= '<div class="row">';

        // Error credenciales inválidas
        if (isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '1') {
            $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> Las credenciales ingresadas son inválidas.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }

        // Mensaje cuenta activada correctamente
        if (isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error']) == '4') {
            $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente!!!</strong> Tu cuenta ha sido activada correctamente. Ahora puedes loguearte y disfrutar de nuestros fabulosos descuentos!!!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }

        $html .= '<div class="col-12">';
        $html .= '<h2>Login</h2>';

        // Campo correo
        $html .= '<div class="mb-3">
              <label for="correo" class="form-label">E-Mail:</label>
              <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="' . esc_attr($_POST['correo'] ?? '') . '" />
              </div>';

        // Campo contraseña
        $html .= '<div class="mb-3">
               <label for="password" class="form-label">Contraseña:</label>
               <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
              </div>';

        // Hidden fields
        $html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce('seg') . '" id="nonce" /><input type="hidden" name="action" value="log-in" />';
        $html .= '<input type="hidden" name="return" value="' . sanitize_text_field($_REQUEST['return'] ?? '') . '" />';

        // Botón login
        $html .= '<hr />';
        $html .= '<a href="javascript:void(0);" class="btn btn-warning" onclick="itc_tienda_login()" title="Entrar"><i class="fas fa-user-lock"></i> Entrar</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</form>';

        // Links adicionales
        $html .= '<hr/><p><a href="' . get_site_url() . '/restablecer" title="Restablecer mi contraseña">Restablecer mi contraseña</a> | <a href="' . get_site_url() . '/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
        $html .= '</div>';

        return $html;
    }
}

// Control acceso a páginas restringidas
add_action('after_setup_theme', function () {
    $url      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url_path = parse_url($url, PHP_URL_PATH);
    $slug     = pathinfo($url_path, PATHINFO_BASENAME);

    if ($slug == 'checkout' or $slug == 'perfil' or $slug == 'verificacion') {
        if (!is_user_logged_in()) {
            echo '<script>window.location="' . get_site_url() . '/login";</script>';
            exit;
        } else {
            if ($slug == 'checkout' or $slug == 'verificacion') {
                $userdata = wp_get_current_user();
                $verificacion = get_user_meta($userdata->ID, 'itc_tienda_verificacion', true);

                if (isset($verificacion['verificacion']) && $verificacion['verificacion'] == '0') {
                    echo '<script>window.location="' . get_site_url() . '/perfil?error=3";</script>';
                    exit;
                }
            }
        }
    }
});
