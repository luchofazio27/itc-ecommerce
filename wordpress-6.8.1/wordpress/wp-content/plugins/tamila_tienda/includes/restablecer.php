<?php 
if(!defined('ABSPATH')) die(); 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';
//shortcode
//[tamila_tienda_restablecer id="1"]
if(!function_exists('tamila_tienda_restablecer_codigo_corto')){
    add_action('init', 'tamila_tienda_restablecer_codigo_corto');
    function tamila_tienda_restablecer_codigo_corto(){
        add_shortcode( 'tamila_tienda_restablecer', 'tamila_tienda_restablecer_codigo_corto_display' );
    }
}
if(!function_exists('tamila_tienda_restablecer_post')){
    function tamila_tienda_restablecer_post(){
        if(isset($_POST['nonce']) and $_POST['action'] == 'restablecer-in' ){ 
            $existe=email_exists( sanitize_text_field($_POST['correo']) );
            if($existe===false){
                wp_safe_redirect( home_url('restablecer')."?error=1" ); exit;
            }
            $payload = [
                'iss' => get_site_url(),
                'aud' => $existe,
                'iat' => time(),
                'exp' => time()*60
            ];
            
            $jwt=tamila_tienda_generate_jwt($payload);
            $url=get_site_url()."/reset?t=".$jwt;
            $mensaje='<h1>Restablecer tu contraseña en '.bloginfo('name').'</h1> 
            Hola haz solicitado restablecer tu contraseña, para eso abre la siguiente URL <br/>'.$url.'<br/> o copia y pega la URL en la barra de direcciones de tu navegador favorito<br/>
            <a href="'.$url.'">'.$url.'</a>
            <hr />';
            tamila_tienda_envia_correo(sanitize_text_field($_POST['correo']), "Restablecer contraseña", $mensaje);
            wp_safe_redirect( home_url('restablecer')."?error=2" ); exit;
        }
    }
}
add_action( 'after_setup_theme', 'tamila_tienda_restablecer_post' );
if(!function_exists('tamila_tienda_restablecer_codigo_corto_display')){
    function tamila_tienda_restablecer_codigo_corto_display($argumentos, $content=""){
        $html='';
        
        $html.='<div class="container"><form action="'.get_site_url().'/restablecer" method="POST" name="tamila_tienda_restablecer_form">';
        
        $html.='<div class="row">';
    
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){
            $html.='<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> El E-Mail ingresado no es válido.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='2'){
            $html.='<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente!!!</strong> Te hemos enviado un correo a la dirección de E-Mail que nos indicaste con las instrucciones a seguir.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
       
        $html.='<div class="col-8">';
        $html.='<h2>Restablecer mi contraseña</h2><p>Indícanos tu correo electrónico para enviarte las instrucciones para que puedas restablecer tu contraseña</p><hr/>';
     
        $html.='<div class="mb-3">
        <label for="correo" class="form-label">E-Mail:</label>
                <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="'.wp_specialchars( $_POST['correo'], 1 ).'" /> 
               </div>';
        
        $html.='<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" /><input type="hidden" name="action" value="restablecer-in" />'; 
        $html.='<input type="hidden" name="return" value="'.sanitize_text_field( $_REQUEST['return'] ).'" />';
        $html.='<hr />';
        $html.='<a href="javascript:void(0);" class="btn btn-warning" onclick="tamila_tienda_restablecer()" title="Enviar"><i class="fas fa-envelope"></i> Enviar</a> ';
        $html.='</div>';
        $html.='</div>';
        $html.='</form>';
        $html.='<hr/><p><a href="'.get_site_url().'/login" title="Ya tengo cuenta">Ya tengo cuenta</a> | <a href="'.get_site_url().'/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
        $html.='</div>';
        
        return $html;
    }
}

