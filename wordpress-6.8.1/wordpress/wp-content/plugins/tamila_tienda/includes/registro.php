<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';
/*
add_action( 'after_setup_theme', function(){
    if(isset($_POST['nonce']) and $_POST['action'] == 'registro-in' ){
        $userdata=[
            'user_login'=>sanitize_text_field($_POST['correo']),
            'user_pass'=>sanitize_text_field($_POST['password']),
            'user_email'=>sanitize_text_field($_POST['correo']),
            'first_name'=>sanitize_text_field($_POST['nombre']),
            'last_name'=>sanitize_text_field($_POST['apellido']),
            'role'=>'subscriber',
            'user_url'=>''
        ];
        $user_id=username_exists( $userdata['user_login'] );
        if(!$user_id && email_exists( $userdata['user_email'] )===false){
            $user_id=wp_insert_user($userdata);
            if(!is_wp_error($user_id)){
                add_user_meta($user_id, 'tamila_tienda_verificacion', ['verificacion'=>'0']);
                wp_safe_redirect( home_url('registro')."?error=1" ); exit;
            }
        }else{
            wp_safe_redirect( home_url('registro')."?error=2" ); exit;
        }
    }
});
*/
add_action( 'after_setup_theme', function(){
    if(isset($_POST['nonce']) and $_POST['action'] == 'registro-in' ){
        $userdata=[
            'user_login'=>sanitize_text_field($_POST['correo']),
            'user_pass'=>sanitize_text_field($_POST['password']),
            'user_email'=>sanitize_text_field($_POST['correo']),
            'first_name'=>sanitize_text_field($_POST['nombre']),
            'last_name'=>sanitize_text_field($_POST['apellido']),
            'role'=>'subscriber',
            'user_url'=>''
        ];
        $user_id=username_exists( $userdata['user_login'] );
        if(!$user_id && email_exists( $userdata['user_email'] )===false){
            $user_id=wp_insert_user($userdata);
            if(!is_wp_error($user_id)){
                add_user_meta($user_id, 'tamila_tienda_verificacion', ['verificacion'=>'0']);
                $payload = [
                    'iss' => get_site_url(),
                    'aud' => $user_id,
                    'iat' => time(),
                    'exp' => time()*60
                ];
                $jwt=tamila_tienda_generate_jwt($payload);
                $url=get_site_url()."/activated?t=".$jwt;
                $mensaje='<h1>Confirma tu cuenta en '.bloginfo('name').'</h1> 
                    Hola '.sanitize_text_field($_POST['nombre']).',por favor haz clic en esta URL para activar tu cuenta <br/>'.$url.'<br/> o copia y pega la URL en la barra de direcciones de tu navegador favorito<br/>
                    <a href="'.$url.'">'.$url.'</a>
                    <hr />';
                tamila_tienda_envia_correo($userdata['user_email'], "Confirmación de Registro", $mensaje);
                wp_safe_redirect( home_url('registro')."?error=1" ); exit;
            }
        }else{
            wp_safe_redirect( home_url('registro')."?error=2" ); exit;
        }
    }
});
//shortcode
//[tamila_tienda_registro id=1]
add_action('init', function(){
    add_shortcode( 'tamila_tienda_registro', 'tamila_tienda_registro_codigo_corto_display' );
});
if(!function_exists('tamila_tienda_registro_codigo_corto_display')){
    function tamila_tienda_registro_codigo_corto_display($argumentos, $content=""){
        //si el usuario está logueado lo mandamos a la página principal
        if(is_user_logged_in()){
            echo '<script>window.location="'.get_site_url().'";</script>';exit;
         }
         $html='';
        
         $html.='<div class="container"><form action="" method="POST" name="tamila_tienda_registro_form">';
         
         $html.='<div class="row">';
         if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='2'){
            $html.='<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> El E-Mail indicado no está disponible, por favor escoje otro.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
         if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){
            $html.='<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Bien!!</strong> Te haz registrado exitosamente. Te hemos enviado un mail para que confirmes tu cuenta y puedas aprovechar todos nuestros descuentos.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
         
         $html.='<div class="col-8">';
         $html.='<h2>Registro</h2>';
         $html.='<div class="mb-3">
                 <label for="nombre" class="form-label">Nombre:</label>
                 <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" /> 
                </div>';
         $html.='<div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Apellido" /> 
               </div>';
         $html.='<div class="mb-3">
         <label for="correo" class="form-label">E-Mail:</label>
                 <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="'.wp_specialchars( $_POST['correo'], 1 ).'" /> 
                </div>';
         $html.='<div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
               </div>';
         $html.='<div class="mb-3">
               <label for="password2" class="form-label">Repetir Contraseña:</label>
               <input type="password" name="password2" id="password2" class="form-control" placeholder="Repetir Contraseña" /> 
              </div>';
         $html.='<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" /><input type="hidden" name="action" value="registro-in" />'; 
         $html.='<hr />';
         $html.='<a href="javascript:void(0);" class="btn btn-warning" onclick="tamila_tienda_registro()" title="Enviar"><i class="fas fa-user"></i> Enviar</a> ';
         $html.='</div>';
         $html.='</div>';
         $html.='</form>';
         $html.='<hr/><p><a href="'.get_site_url().'/login" title="Ya tengo cuenta">Ya tengo cuenta</a></p>';
         $html.='</div>';
         
         return $html;

    }
}