<?php 
if(!defined('ABSPATH')) die();
//JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//[tamila_tienda_reset id="1"]
if(!function_exists('tamila_tienda_reset_codigo_corto')){
    add_action('init', 'tamila_tienda_reset_codigo_corto');
    function tamila_tienda_reset_codigo_corto(){
        add_shortcode( 'tamila_tienda_reset', 'tamila_tienda_reset_codigo_corto_display' );
    }
}
if(!function_exists('tamila_tienda_reset_post')){
    function tamila_tienda_reset_post(){
        if(isset($_POST['nonce']) and $_POST['action'] == 'reset-in' ){
            if(!isset($_GET['t'])){
            
                wp_safe_redirect( home_url('error') ); exit;
            }
            global $wpdb;
            $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}tamila_tienda_variables_globales where id in(5);", ARRAY_A);
            require 'vendor/autoload.php';
            try {
                $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512'));
                wp_set_password( sanitize_text_field($_POST['password']), $decode->aud);
                wp_safe_redirect( home_url('reset')."?error=3&t=".$_GET['t'] ); exit;
            } catch (\Throwable $th) {
                wp_safe_redirect( home_url('reset')."?error=2&t=".$_GET['t'] ); exit;
            }
            
        }
    }
}
add_action( 'after_setup_theme', 'tamila_tienda_reset_post' );
if(!function_exists('tamila_tienda_reset_codigo_corto_display')){
    function tamila_tienda_reset_codigo_corto_display($argumentos, $content=""){
        if(!isset($_GET['t'])){
            
            wp_safe_redirect( home_url('error') ); exit;
        }
        global $wpdb;
        $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}tamila_tienda_variables_globales where id in(5);", ARRAY_A);
        require 'vendor/autoload.php';
        
        try {
            
            $decode = JWT::decode($_GET['t'], new Key($datos[0]['valor'], 'HS512'));
            $html='';
        
        $html.='<div class="container"><form action="'.get_site_url().'/reset/?t='.$_GET['t'].'" method="POST" name="tamila_tienda_reset_form">';
        
        $html.='<div class="row">';
    
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='2'){
            $html.='<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> Ocurrió un error inesperado, por favor vuelve a intentarlo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='3'){
            $html.='<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente</strong> Haz cambiado tu contraseña exitosamente, ahora loguéate y aprovecha todos nuestros descuentos.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
       
        $html.='<div class="col-8">';
        $html.='<h2>Restablecer mi contraseña</h2><p>Necesitamos que te crees una nueva contraseña</p><hr/>';
     
        $html.='<div class="mb-3">
        <label for="password" class="form-label">Contraseña:</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
       </div>';
 $html.='<div class="mb-3">
       <label for="password2" class="form-label">Repetir Contraseña:</label>
       <input type="password" name="password2" id="password2" class="form-control" placeholder="Repetir Contraseña" /> 
      </div>';
        
        $html.='<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" /><input type="hidden" name="action" value="reset-in" />'; 
        $html.='<input type="hidden" name="return" value="'.sanitize_text_field( $_REQUEST['return'] ).'" />';
        $html.='<hr />';
        $html.='<a href="javascript:void(0);" class="btn btn-warning" onclick="tamila_tienda_reset()" title="Enviar"><i class="fas fa-lock"></i> Enviar</a> ';
        $html.='</div>';
        $html.='</div>';
        $html.='</form>';
        $html.='<hr/><p><a href="'.get_site_url().'/login" title="Ya tengo cuenta">Ya tengo cuenta</a> | <a href="'.get_site_url().'/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
        $html.='</div>';
        
        return $html;
            
        } catch (\Throwable $th) {
           //print_r($th);
           echo '<script>window.location="'.get_site_url().'/error";</script>';exit;
        }
    }
}