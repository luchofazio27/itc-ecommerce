<?php 
if(!defined('ABSPATH')) die(); // Evita el acceso directo al archivo si no está en WordPress

// Hook que se ejecuta después de que el tema esté configurado
add_action('after_setup_theme', function(){
    // Verifica si se envió el formulario con el nonce y la acción específica
    if(isset($_POST['nonce']) and $_POST['action']=='perfil-in'){
        $userdata = wp_get_current_user(); // Obtiene el usuario actual

        // Actualiza nombre y apellido del usuario
        $user_id = wp_update_user([
            'ID'         => $userdata->ID,
            'first_name' => sanitize_text_field($_POST['nombre']),
            'last_name'  => sanitize_text_field($_POST['apellido'])
        ]);

        // Si la actualización fue exitosa
        if(!is_wp_error($user_id)){
            // Si el campo contraseña no está vacío, actualiza la contraseña
            if(filter_var(trim($_POST['password'])) == true){
                wp_set_password(sanitize_text_field($_POST['password']), $userdata->ID);
            }
            // Redirige con mensaje de éxito
            wp_redirect(home_url('perfil')."?error=1"); 
            exit;
        } else {
            // Redirige con mensaje de error
            wp_redirect(home_url('perfil')."?error=2"); 
            exit;
        }
    }
});

// Shortcode
// [itc_tienda_perfil id=1]
add_action('init', function(){
    // Registra el shortcode y la función que lo procesa
    add_shortcode('itc_tienda_perfil', 'itc_tienda_perfil_codigo_corto_display');
});

// Si la función no existe, la define
if(!function_exists('itc_tienda_perfil_codigo_corto_display')){
    function itc_tienda_perfil_codigo_corto_display($argumentos, $content=""){
        $userdata = wp_get_current_user(); // Obtiene datos del usuario actual
       ?>
        <div class="container"> <!-- Contenedor principal -->
            <div class="row">
                <div class="col-10 ">
                    <hr/>
                    <h2>Mi perfil</h2>

                    <!-- Acordeón -->
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                <!-- Botón para mostrar sección "Mis datos" -->
                                <button class="accordion-button bg-secondary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                    <i class="fas fa-user"></i>&nbsp;Mis datos
                                </button>
                            </h2>

                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                <div class="accordion-body">
                                    <!-- Formulario de perfil -->
                                    <div class="row">
                                        <form action="" method="POST" name="itc_tienda_registro_form">
                                            <h1>Mis datos</h1>

                                            <?php 
                                            // Mensaje si la modificación fue exitosa
                                            if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='1'){ ?>
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    Se modificó el registro exitosamente.
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php } ?>

                                            <?php 
                                            // Mensaje si hubo error en la modificación
                                            if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='2'){ ?>
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    Ocurrió un error inesperado, por favor vuelva a intentarlo.
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php } ?>

                                            <?php 
                                            // Mensaje si la cuenta no está verificada
                                            if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='3'){ ?>
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    Debes verificar tu cuenta para poder continuar.
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php } ?>

                                            <hr/>

                                            <!-- Campo de correo, solo lectura -->
                                            <div class="mb-3">
                                                <label for="correo" class="form-label">E-Mail:</label>
                                                <input type="text" name="correo" id="correo" class="form-control" placeholder="E-Mail" value="<?php echo $userdata->user_email?>" readonly="true" />
                                            </div>

                                            <!-- Campo de nombre -->
                                            <div class="mb-3">
                                                <label for="nombre" class="form-label">Nombre:</label>
                                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" value="<?php echo $userdata->user_firstname?>" /> 
                                            </div>

                                            <!-- Campo de apellido -->
                                            <div class="mb-3">
                                                <label for="apellido" class="form-label">Apellido:</label>
                                                <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Apellido" value="<?php echo $userdata->user_lastname?>" /> 
                                            </div>

                                            <!-- Campo de contraseña -->
                                            <div class="mb-3">
                                                <label for="password" class="form-label">
                                                    Contraseña <strong>(Si modificas tu contraseña deberás volver a loguearte por motivos de seguridad)</strong>:
                                                </label>
                                                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" /> 
                                            </div>

                                            <!-- Confirmación de contraseña -->
                                            <div class="mb-3">
                                                <label for="password2" class="form-label">Repetir Contraseña:</label>
                                                <input type="password" name="password2" id="password2" class="form-control" placeholder="Repetir Contraseña" /> 
                                            </div>

                                            <hr/>

                                            <!-- Campos ocultos para seguridad -->
                                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
                                            <input type="hidden" name="action" value="perfil-in" />

                                            <!-- Botón que llama función JavaScript para enviar el formulario -->
                                            <a href="javascript:void(0);" class="btn btn-warning" onclick="itc_tienda_perfil()" title="Entrar">
                                                <i class="fas fa-edit"></i> Entrar
                                            </a> 
                                        </form>
                                    </div>
                                    <!-- /Formulario -->
                                </div>
                            </div>
                        </div>

                        <!-- Segunda sección del acordeón -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                <button class="accordion-button bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                    <i class="fas fa-list"></i>&nbsp;Mis compras
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                <div class="accordion-body">
                                    <strong>This is the second item's accordion body.</strong> Contenido de ejemplo para la sección "Mis compras".
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Acordeón -->
                </div>
            </div>
        </div>
        <?php
    }
}
