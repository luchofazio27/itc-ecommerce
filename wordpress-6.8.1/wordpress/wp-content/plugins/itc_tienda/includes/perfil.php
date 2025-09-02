<?php 
if(!defined('ABSPATH')) die();

add_action('after_setup_theme', function(){
    if(isset($_POST['nonce']) and $_POST['action']=='perfil-in'){
        $userdata = wp_get_current_user();

        $user_id = wp_update_user([
            'ID'         => $userdata->ID,
            'first_name' => sanitize_text_field($_POST['nombre']),
            'last_name'  => sanitize_text_field($_POST['apellido'])
        ]);

        if(!is_wp_error($user_id)){
            if(filter_var(trim($_POST['password'])) == true){
                wp_set_password(sanitize_text_field($_POST['password']), $userdata->ID);
            }
            wp_redirect(home_url('perfil')."?error=1"); 
            exit;
        } else {
            wp_redirect(home_url('perfil')."?error=2"); 
            exit;
        }
    }
});

add_action('init', function(){
    add_shortcode('itc_tienda_perfil', 'itc_tienda_perfil_codigo_corto_display');
});

if(!function_exists('itc_tienda_perfil_codigo_corto_display')){
    function itc_tienda_perfil_codigo_corto_display($argumentos, $content=""){
        $userdata = wp_get_current_user(); 
       ?>
        <div class="container-fluid px-0"> <!-- ahora ocupa todo el ancho -->
            <div class="row justify-content-center">
                <div class="col-10 col-md-8">
                    
                    

                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item">
                            

                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                <div class="accordion-body">
                                    <form action="" method="POST" name="itc_tienda_registro_form" class="itc-form-container">
                                        <h1>Mis datos</h1>

                                        <?php if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='1'){ ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                Se modificó el registro exitosamente.
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php } ?>

                                        <?php if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='2'){ ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                Ocurrió un error inesperado.
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php } ?>

                                        <div class="mb-3">
                                            <label for="correo" class="form-label">E-Mail:</label>
                                            <input type="text" name="correo" id="correo" class="form-control" value="<?php echo $userdata->user_email?>" readonly />
                                        </div>

                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre:</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo $userdata->user_firstname?>" /> 
                                        </div>

                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido:</label>
                                            <input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo $userdata->user_lastname?>" /> 
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Contraseña:</label>
                                            <input type="password" name="password" id="password" class="form-control" /> 
                                        </div>

                                        <div class="mb-3">
                                            <label for="password2" class="form-label">Repetir Contraseña:</label>
                                            <input type="password" name="password2" id="password2" class="form-control" /> 
                                        </div>

                                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
                                        <input type="hidden" name="action" value="perfil-in" />

                                        <a href="javascript:void(0);" class="btn btn-warning" onclick="itc_tienda_perfil()">
                                            <i class="fas fa-edit"></i> Guardar cambios
                                        </a> 
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
/* Reset de márgenes y paddings */
body, html {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden;
}

/* Formulario */
.itc-form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 25px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

/* Footer */
.footer-custom {
  width: 100%;
  margin: 0;
  padding: 20px;
  background: #333;
  color: #fff;
}
</style>

        <?php
    }
}
