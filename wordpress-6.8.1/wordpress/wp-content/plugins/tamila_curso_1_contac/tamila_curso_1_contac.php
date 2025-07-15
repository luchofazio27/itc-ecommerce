<?php
/*
Plugin Name: Tamila Curso  1 Contact
Plugin URI: https://www.cesarcancino.com/
Description: Este plugin es para crear un formulario de contactos
Version: 1.0.1
Author: César Cancino
Author URI: https://www.cesarcancino.com/
License: GPL
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tamila_curso_1_contact 
*/
if(!defined('ABSPATH')) die();
//para envío de correo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(!function_exists('tamila_curso_1_contact_activar')){
    function tamila_curso_1_contact_activar(){
        global $wpdb;
        $sql="create table if not exists 
            {$wpdb->prefix}tamila_curso_1_contact(
                id int not null auto_increment,
                nombre varchar(200),
                correo varchar(100),
                fecha date,
                primary key(id) 
            );          
        "; 
        $wpdb->query($sql);
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact add index(`nombre`);");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact add index(`correo`);");
        $wpdb->query("create table if not exists 
        {$wpdb->prefix}tamila_curso_1_contact_respuestas
        (
        id int not null auto_increment,
        tamila_curso_1_contact_id int,
        nombre varchar(255) not null,
        correo varchar(255) not null,
        telefono varchar(255) not null,
        mensaje text not null,
        fecha date,
        primary key (id)
        ); 
        ");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact_respuestas add constraint fk_contact_id foreign key (tamila_curso_1_contact_id) references {$wpdb->prefix}tamila_curso_1_contact(id);");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact_respuestas add index(`nombre`);");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact_respuestas add index(`correo`);");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact_respuestas add index(`telefono`);");
        $wpdb->query("alter table {$wpdb->prefix}tamila_curso_1_contact_respuestas add index(`mensaje`);");

    }
}
if(!function_exists('tamila_curso_1_contact_desactivar')){
    function tamila_curso_1_contact_desactivar(){

        #limpiador de enlaces permanentes
        flush_rewrite_rules( );
    }
}
register_activation_hook(__FILE__, 'tamila_curso_1_contact_activar');
register_activation_hook(__FILE__, 'tamila_curso_1_contact_desactivar');

#enqueue
add_action('admin_enqueue_scripts', function($hook){
    
        if($hook=='tamila_curso_1_contac/includes/listar.php'){
            wp_enqueue_style( "bootstrapcss",  plugins_url( 'assets/css/bootstrap.min.css', __FILE__ ) );
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
            wp_enqueue_style( "sweetalert2",  plugins_url( 'assets/css/sweetalert2.css', __FILE__ ) );
            wp_enqueue_script( "bootstrapjs",  plugins_url( 'assets/js/bootstrap.min.js', __FILE__ ), array('jquery')); 
            wp_enqueue_script( "sweetalert2",  plugins_url( 'assets/js/sweetalert2.js', __FILE__ ), array('jquery'));
            wp_enqueue_script( "funcionesj",  plugins_url( 'assets/js/funciones.js', __FILE__ ) );
            wp_localize_script('funcionesj','datosajax',[
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('seg')
            ]);
        }
       
});
#cargamos el menú
if(!function_exists('tamila_curso_1_contact_menu')){
    add_action('admin_menu', 'tamila_curso_1_contact_menu');
    function tamila_curso_1_contact_menu(){
        add_menu_page(
            "Tamila form contact",
            "Tamila form contact",
            "manage_options",
            plugin_dir_path( __FILE__ )."includes/listar.php", 
            null,
            "dashicons-menu-alt",
            137
        );
    }
}
//registrar el shortcode
//[tamila_curso_1_contact id=2]
add_action('init', function(){
    add_shortcode( 'tamila_curso_1_contact', 'tamila_curso_1_contact_display' );
});
if(!function_exists('tamila_curso_1_contact_display')){
    function tamila_curso_1_contact_display($argumentos, $content=""){
        global $wpdb;

        if(isset($_POST['nonce'])){
            $data=[
                'tamila_curso_1_contact_id'=>$argumentos['id'],
                'nombre' => sanitize_text_field($_POST['nombre']),
                'correo' => sanitize_text_field($_POST['correo']),
                'telefono' => sanitize_text_field($_POST['telefono']),
                'mensaje' => sanitize_text_field($_POST['mensaje']),
                'fecha'=>date('Y-m-d')
            ];
            $wpdb->insert("{$wpdb->prefix}tamila_curso_1_contact_respuestas", $data);
            //enviar el correo
            tamila_curso_1_contact_send_correo($argumentos['id'], sanitize_text_field($_POST['nombre']), sanitize_text_field($_POST['correo']), sanitize_text_field($_POST['telefono']), sanitize_text_field($_POST['mensaje']));
            //redireccionar al usuario
            ?>
            <script>
                Swal.fire({
                icon: 'success',
                title: 'OK',
                text: 'Se envió tu mensaje exitosamente, nos pondremos en contacto contigo a la brevedad',
            });
            setInterval(()=>{
                window.location=location.href;
            });
            </script>
            <?php
        }
        ?>
    <script>
        function validaCorreo(valor) {
            if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
            return true;
            } else {
            return false;
            }
            }
        function tamila_form_contact( )
        {
            var form=document.tamila_curso_1_contact_form;
            if(form.nombre.value==0)
            { 
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El campo nombre es obligatorio',
            });
            form.nombre.value='';
            return false;
            }
            
            if(form.correo.value==0)
            { 
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El campo E-Mail es obligatorio',
            });
            form.correo.value='';
            return false;
            }
            if(validaCorreo(form.correo.value)==false){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'El E-Mail ingresado no es válido',
                });
                form.correo.value='';
                return false;
            }
            if(form.telefono.value==0)
            { 
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El campo Teléfono es obligatorio',
            });
            form.telefono.value='';
            return false;
            }
            if(form.mensaje.value==0)
            { 
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'El campo mensaje es obligatorio',
            });
            form.mensaje.value='';
            return false;
            }
            form.submit();
        }
    </script>
        <?php
		$nonce = wp_create_nonce('seg');
        $html='';
        $html.='<div class="container"><form action="" method="POST" name="tamila_curso_1_contact_form">';
       
       $html.='<div class="row">';
       $html.='<div class="col-8">';
       $html.='<h5>Completa el siguiente formulario y nos pondremos en contacto contigo</h5>';
       $html.='<div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" /> 
               </div>';
       $html.='<div class="mb-3">
       <label for="nombre" class="form-label">E-Mail:</label>
               <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" /> 
              </div>';
       $html.='<div class="mb-3">
              <label for="nombre" class="form-label">Teléfono:</label>
              <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Telefono" /> 
             </div>';
       $html.='<div class="mb-3">
             <label for="nombre" class="form-label">Mensaje:</label>
             <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Mensaje"></textarea>
            </div>';
       $html.='<input type="hidden" name="nonce" value="'.$nonce.'" id="nonce" />'; 
       $html.='<hr />';
       $html.='<a href="javascript:void(0);" class="btn btn-warning" onclick="tamila_form_contact()"><i class="fas fa-envelope"></i> Enviar</a> ';
       $html.='</div>';
       $html.='</div>';
       $html.='</form></div>';
       return $html;
    }
}
if(!function_exists('tamila_curso_1_contact_send_correo')){
    function tamila_curso_1_contact_send_correo($id, $nombre, $correo, $telefono, $mensaje){
        
        global $wpdb;
        $datos=$wpdb->get_results("select correo from {$wpdb->prefix}tamila_curso_1_contact where id='{$id}';", ARRAY_A);
        
        require 'vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            $mail->STMTDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host ='smtp.dreamhost.com';
            $mail->SMTPAuth   = true;
            $mail->Username='noreply@agendahoras.cl';
            $mail->Password='khdwJAXysB';
            $mail->Port = 587;

            $mail->setFrom('noreply@agendahoras.cl', "Curso MVP");
            $mail->addAddress($datos[0]['correo'], utf8_decode(bloginfo('name')));

            $mail->isHTML(true);
            $mail->Subject='Asunto de el mail';
            $mail->Body = utf8_decode(
                                        '<h1>Mensaje desde sitio web</h1> <hr />
                                        <ul>
                                            <li>Nombre: '.$nombre.'</li>
                                            <li>E-Mail: '.$correo.'</li>
                                            <li>Teléfono: '.$telefono.'</li>
                                            <li>Mensaje: '.$mensaje.'</li>
                                            
                                        </ul>
                                        '
                                    );

            $mail->send();
            return true;

        } catch (Exception $e) {
        return false;
        }
    }
}
if(!function_exists('tamila_curso_1_contac_respuestas_ajax')){
    function tamila_curso_1_contac_respuestas_ajax(){
        $nonce = $_POST['nonce'];
        if(!wp_verify_nonce($nonce, 'seg')){
            die('no tiene permisos para ejecutar ese ajax');
        }
        global $wpdb;
        $query="select * from {$wpdb->prefix}tamila_curso_1_contact_respuestas where tamila_curso_1_contact_id='".sanitize_text_field($_POST['id'])."' order by id desc;";
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
    add_action('wp_ajax_tamila_curso_1_contac_respuestas_ajax', 'tamila_curso_1_contac_respuestas_ajax');
}