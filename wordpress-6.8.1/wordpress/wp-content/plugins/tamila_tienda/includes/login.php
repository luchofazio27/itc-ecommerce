<?php 
if(!defined('ABSPATH')) die();
//[tamila_tienda_login]
add_action('init', function(){
    add_shortcode( 'tamila_tienda_login', 'tamila_tienda_login_codigo_corto_display' );
});

add_action('after_setup_theme', function(){
    if(isset($_POST['nonce']) and $_POST['action'] == 'log-in' ){
        $login = wp_signon( array( 'user_login' => sanitize_text_field( $_POST['correo'] ), 'user_password' => sanitize_text_field( $_POST['password'] ), 'remember' => false ), false );
        if($login->ID){
            wp_clear_auth_cookie();
            do_action('wp_login', $login->ID);
            wp_set_current_user($login->ID);
            wp_set_auth_cookie($login->ID, true); 
            if(empty($_POST['return'])){
                echo '<script>window.location="'.get_site_url().'/perfil";</script>';exit;
                //wp_safe_redirect( home_url('perfil') );
             }else{
                //wp_safe_redirect( home_url('tienda')."/".base64_decode($_POST['return']) );
                echo '<script>window.location="'.get_site_url().'/tienda/'.base64_decode($_POST['return']).'";</script>';exit;
            }
        }else{
            wp_safe_redirect( home_url('login')."?error=1" ); exit;
        }
    }
});

if(!function_exists('tamila_tienda_login_codigo_corto_display')){
    function tamila_tienda_login_codigo_corto_display($argumentos, $content=""){
        if ( is_user_logged_in() ) {
            echo '<script>window.location="'.get_site_url().'";</script>';exit;
        }
        $html='';
        
        $html.='<div class="container"><form action="'.get_site_url().'/login" method="POST" name="tamila_tienda_login_form">';
        
        $html.='<div class="row">';
    
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){
            $html.='<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Ups</strong> Las credenciales ingresadas son inválidas.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='4'){
            $html.='<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente!!!</strong> Tu cuenta ha sido activada correctamente. Ahora puedes loguearte y disfrutar de nuestros fabulosos descuentos!!!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
       
        $html.='<div class="col-8">';
        $html.='<h2>Login</h2>';
     
        $html.='<div class="mb-3">
        <label for="correo" class="form-label">E-Mail:</label>
                <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="'.wp_specialchars( $_POST['correo'], 1 ).'" /> 
               </div>';
        $html.='<div class="mb-3">
               <label for="password" class="form-label">Contraseña:</label>
               <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
              </div>';
        $html.='<input type="hidden" name="nonce" value="'.wp_create_nonce('seg').'" id="nonce" /><input type="hidden" name="action" value="log-in" />'; 
        $html.='<input type="hidden" name="return" value="'.sanitize_text_field( $_REQUEST['return'] ).'" />';
        $html.='<hr />';
        $html.='<a href="javascript:void(0);" class="btn btn-warning" onclick="tamila_tienda_login()" title="Entrar"><i class="fas fa-user-lock"></i> Entrar</a> ';
        $html.='</div>';
        $html.='</div>';
        $html.='</form>';
        $html.='<hr/><p><a href="'.get_site_url().'/restablecer" title="Restablecer mi contraseña">Restablecer mi contraseña</a> | <a href="'.get_site_url().'/registro" title="No tienes cuenta? Regístrate aquí">No tienes cuenta? Regístrate aquí</a></p>';
        $html.='</div>';
        
        return $html;
    }
}
//gestión del login
add_action('after_setup_theme', function(){
    $url      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url_path = parse_url( $url, PHP_URL_PATH );
    $slug = pathinfo( $url_path, PATHINFO_BASENAME );
    if($slug=='checkout' or $slug=='perfil' or $slug=='verificacion'){
        if ( !is_user_logged_in() ) {
            echo '<script>window.location="'.get_site_url().'/login";</script>';exit;
           
        }else{
            if($slug=='checkout' or $slug=='verificacion'){ 
                if(get_user_meta( $userdata->ID, 'tamila_tienda_verificacion' , true )['verificacion']=='0'){
                     
                    echo '<script>window.location="'.get_site_url().'/perfil?error=3";</script>';exit;
                }
            }
        }
    }
});