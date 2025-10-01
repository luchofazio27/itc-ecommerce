<?php
/*
Plugin Name: ITC Contact
Plugin URI: https://www.liffdomotic.com/
Description: Este plugin es para crear un formulario de contactos
Version: 1.0.1
Author: Liff Domotic
Author URI: https://www.liffdomotic.com/
License: GPL
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: itc_contact
*/

if (!defined('ABSPATH')) die();
//para envío de correo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('itc_contact_activar')) {
    function itc_contact_activar()
    {
        global $wpdb;
        $sql = "create table if not exists 
            {$wpdb->prefix}itc_contact(
                id int not null auto_increment,
                nombre varchar(200),
                correo varchar(100),
                fecha date,
                primary key(id) 
            );          
        ";
        $wpdb->query($sql);
        $wpdb->query("alter table {$wpdb->prefix}itc_contact add index(`nombre`);");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact add index(`correo`);");
        $wpdb->query("create table if not exists 
        {$wpdb->prefix}itc_contact_respuestas
        (
        id int not null auto_increment,
        itc_contact_id int,
        nombre varchar(255) not null,
        correo varchar(255) not null,
        telefono varchar(255) not null,
        mensaje text not null,
        fecha date,
        primary key (id)
        ); 
        ");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact_respuestas add constraint fk_itc_contact_id foreign key (itc_contact_id) references {$wpdb->prefix}itc_contact(id);");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact_respuestas add index(`nombre`);");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact_respuestas add index(`correo`);");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact_respuestas add index(`telefono`);");
        $wpdb->query("alter table {$wpdb->prefix}itc_contact_respuestas add index(`mensaje`);");
    }
}
if (!function_exists('itc_contact_desactivar')) {
    function itc_contact_desactivar()
    {

        #limpiador de enlaces permanentes
        flush_rewrite_rules();
    }
}
register_activation_hook(__FILE__, 'itc_contact_activar');
register_activation_hook(__FILE__, 'itc_contact_desactivar');

#enqueue
add_action('admin_enqueue_scripts', function ($hook) {

    if ($hook == 'itc_contact/includes/listar.php') {
        wp_enqueue_style("bootstrapcss",  plugins_url('assets/css/bootstrap.min.css', __FILE__));
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        wp_enqueue_style("sweetalert2",  plugins_url('assets/css/sweetalert2.css', __FILE__));
        wp_enqueue_script("bootstrapjs",  plugins_url('assets/js/bootstrap.min.js', __FILE__), array('jquery'));
        wp_enqueue_script("sweetalert2",  plugins_url('assets/js/sweetalert2.js', __FILE__), array('jquery'));
        wp_enqueue_script("funcionesj",  plugins_url('assets/js/funciones.js', __FILE__));
        wp_localize_script('funcionesj', 'datosajax', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('seg')
        ]);
    }
});

#cargamos el menú
if (!function_exists('itc_contact_menu')) {
    add_action('admin_menu', 'itc_contact_menu');
    function itc_contact_menu()
    {
        add_menu_page(
            "ITC Form Contact",
            "ITC Form Contact",
            "manage_options",
            plugin_dir_path(__FILE__) . "includes/listar.php",
            null,
            "dashicons-menu-alt",
            137
        );
    }
}
//registrar el shortcode
//[itc_contact id=2]
add_action('init', function () {
    add_shortcode('itc_contact', 'itc_contact_display');
});
if (!function_exists('itc_contact_display')) {
    function itc_contact_display($argumentos, $content = "")
    {
        global $wpdb;

        if (isset($_POST['nonce'])) {
            $data = [
                'itc_contact_id' => $argumentos['id'],
                'nombre' => sanitize_text_field($_POST['nombre']),
                'correo' => sanitize_text_field($_POST['correo']),
                'telefono' => sanitize_text_field($_POST['telefono']),
                'mensaje' => sanitize_text_field($_POST['mensaje']),
                'fecha' => date('Y-m-d')
            ];
            $wpdb->insert("{$wpdb->prefix}itc_contact_respuestas", $data);
            //enviar el correo
            itc_contact_send_correo($argumentos['id'], sanitize_text_field($_POST['nombre']), sanitize_text_field($_POST['correo']), sanitize_text_field($_POST['telefono']), sanitize_text_field($_POST['mensaje']));
            //redireccionar al usuario
?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'OK',
                    text: 'Se envió tu mensaje exitosamente, nos pondremos en contacto contigo a la brevedad',
                });
                setTimeout(() => {
    window.location = location.href;
}, 3000);
            </script>
        <?php
        }
        ?>
        <script>
            function validaCorreo(valor) {
                if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<script>()[\]\.,;:\s@\"]{2,})$/i.test(valor)) {
                    return true;
                } else {
                    return false;
                }
            }

            function itc_form_contact() {
                var form = document.itc_contact_form;
                if (form.nombre.value == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'El campo nombre es obligatorio',
                    });
                    form.nombre.value = '';
                    return false;
                }

                if (form.correo.value == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'El campo E-Mail es obligatorio',
                    });
                    form.correo.value = '';
                    return false;
                }
                if (validaCorreo(form.correo.value) == false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'El E-Mail ingresado no es válido',
                    });
                    form.correo.value = '';
                    return false;
                }
                if (form.telefono.value == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'El campo Teléfono es obligatorio',
                    });
                    form.telefono.value = '';
                    return false;
                }
                if (form.mensaje.value == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'El campo mensaje es obligatorio',
                    });
                    form.mensaje.value = '';
                    return false;
                }
                form.submit();
            }
        </script>
<?php
        $nonce = wp_create_nonce('seg');
        $html  = '<div class="container my-5">';
$html .= '<div class="row justify-content-center">';
$html .= '<div class="col-lg-8 col-md-10">';
$html .= '<div class="card shadow-lg border-0 rounded-3">';
$html .= '<div class="card-body p-4">';
$html .= '<h4 class="text-center mb-4 text-dark">Completa el siguiente formulario y nos pondremos en contacto contigo</h4>';

$html .= '<form action="" method="POST" name="itc_contact_form">';
$html .= '<div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" /> 
          </div>';
$html .= '<div class="mb-3">
            <label for="correo" class="form-label">E-Mail:</label>
            <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" /> 
          </div>';
$html .= '<div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono" /> 
          </div>';
$html .= '<div class="mb-3">
            <label for="mensaje" class="form-label">Mensaje:</label>
            <textarea class="form-control" name="mensaje" id="mensaje" rows="4" placeholder="Escribe tu mensaje aquí..."></textarea>
          </div>';
$html .= '<input type="hidden" name="nonce" value="' . $nonce . '" id="nonce" />';
$html .= '<div class="d-grid gap-2">';
$html .= '<a href="javascript:void(0);" class="btn btn-warning btn-lg text-white fw-bold" onclick="itc_form_contact()"><i class="fas fa-envelope"></i> Enviar</a>';
$html .= '</div>';
$html .= '</form>';

$html .= '</div>'; // card-body
$html .= '</div>'; // card
$html .= '</div>'; // col
$html .= '</div>'; // row
$html .= '</div>'; // container

        return $html;
    }
}
if (!function_exists('itc_contact_send_correo')) {
    function itc_contact_send_correo($id, $nombre, $correo, $telefono, $mensaje)
    {
        global $wpdb;

        // Guardar nivel de reporte actual
        $oldErrorReporting = error_reporting();
        // Ocultar warnings y notices mientras se envía el correo
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

        // Obtener correo destino desde la tabla itc_contact
        $datos = $wpdb->get_results("SELECT correo FROM {$wpdb->prefix}itc_contact WHERE id='{$id}';", ARRAY_A);

        // Obtener configuración SMTP desde variables globales
        $smtp = $wpdb->get_results(
            "SELECT nombre, valor FROM {$wpdb->prefix}itc_tienda_variables_globales WHERE id IN (1,2,3,4);",
            ARRAY_A
        );

        require_once __DIR__ . '/vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // DEBUG OFF
            $mail->isSMTP();
            $mail->Host       = $smtp[0]['valor']; // smtp_server
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp[1]['valor']; // smtp_user
            $mail->Password   = $smtp[2]['valor']; // smtp_password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $smtp[3]['valor']; // smtp_port
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($smtp[1]['valor'], "ITC ecommerce");
            $mail->addAddress($datos[0]['correo'], get_bloginfo('name'));

            // Opciones SSL solo para testing/local (descomentar si es necesario)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->isHTML(true);
            $mail->Subject = 'Consulta desde tienda ITC';
            $mail->Body = '
                <h1>Mensaje desde sitio web</h1> <hr />
                <ul>
                <li>Nombre: ' . $nombre . '</li>
                <li>E-Mail: ' . $correo . '</li>
                <li>Teléfono: ' . $telefono . '</li>
                <li>Mensaje: ' . $mensaje . '</li>
                </ul>';

            $mail->send();
            $success = true;

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            $success = false;
        }

        // Restaurar nivel de reporte original
        error_reporting($oldErrorReporting);

        return $success;
    }
}


if(!function_exists('itc_contact_respuestas_ajax')){
    function itc_contact_respuestas_ajax(){
        $nonce = $_POST['nonce'];
        if(!wp_verify_nonce($nonce, 'seg')){
            die('no tiene permisos para ejecutar ese ajax');
        }
        global $wpdb;
        $query="select * from {$wpdb->prefix}itc_contact_respuestas where itc_contact_id='".sanitize_text_field($_POST['id'])."' order by id desc;";
        $datos=$wpdb->get_results($query, ARRAY_A);
        ?>
         <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>E-Mail</th>
                    <th>Teléfono</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($datos as $dato){
                    ?>
                    <tr>
                        <td><?php echo $dato['nombre'];?></td>
                        <td><?php echo $dato['correo'];?></td>
                        <td><?php echo $dato['telefono'];?></td>
                        <td><?php echo $dato['mensaje'];?></td>
                        <td><?php echo $dato['fecha'];?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
        die();
    }
    add_action('wp_ajax_itc_contact_respuestas_ajax', 'itc_contact_respuestas_ajax');
}


