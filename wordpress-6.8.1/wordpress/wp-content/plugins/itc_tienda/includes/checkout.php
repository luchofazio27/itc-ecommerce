<?php 
// Evita acceso directo al archivo
if(!defined('ABSPATH')) die();

// Acción que se ejecuta después de cargar el tema
add_action('after_setup_theme', function(){
    global $wpdb; // Se declara el objeto global para consultas a la base de datos

    // Si se recibe un formulario para limpiar el carrito
    if(isset($_POST['nonce']) and $_POST['action']=='limpiar-carrito-in'){
        // Actualiza el carrito en la base de datos, cambiando el estado a 2 (carrito limpio)
        $wpdb->query("update {$wpdb->prefix}itc_tienda_carro set estado_id=2 where id='".sanitize_text_field($_POST['carro_id'])."';");
        // Redirige al checkout con un error de carrito vacío
        wp_safe_redirect( home_url('checkout')."?error=2" ); exit;
    }

    // Si se recibe un formulario de checkout
    if(isset($_POST['nonce']) and $_POST['action']=='checkout-in'){
        // Actualiza el carrito en la base de datos con los datos del formulario (dirección, teléfono, etc.)
        $sql="update {$wpdb->prefix}itc_tienda_carro
        set 
        estado_id=6, 
        direccion='".sanitize_text_field($_POST['direccion'])." ".sanitize_text_field($_POST['direccion2'])."', 
        telefono='".sanitize_text_field($_POST['telefono'])."', 
        observaciones='".sanitize_text_field($_POST['observaciones'])."',
         tipo_pago='".sanitize_text_field($_POST['pasarela'])."', 
         ciudad='".sanitize_text_field($_POST['ciudad'])."' 
         where 
         id='".sanitize_text_field($_POST['carro_id'])."';";
         $wpdb->query($sql); // Ejecuta el update
         wp_safe_redirect( home_url('verificacion') ); exit; // Redirige a página de verificación
    }
});

// [itc_tienda_checkout id="1"] → shortcode para usar en páginas
add_action('init', function(){
    add_shortcode( 'itc_tienda_checkout', 'itc_tienda_checkout_codigo_corto_display' );
});

// Si la función aún no existe la definimos
if(!function_exists('itc_tienda_checkout_codigo_corto_display')){
    function itc_tienda_checkout_codigo_corto_display($argumentos, $content=""){
        // Si el usuario no está logueado lo redirige al home
        if ( !is_user_logged_in() ) {
            echo '<script>window.location="'.get_site_url().'";</script>';exit;
        }

        $userdata=wp_get_current_user(); // Obtiene datos del usuario actual
        global $wpdb;

        // Obtiene lista de países de la BD
        $paises=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_pais;");

        // Obtiene pasarelas de pago habilitadas
        $pasarelas=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas where estado_id=1;");

        // Obtiene las compras/carrito del usuario
        $compras=$wpdb->get_results("select 
        {$wpdb->prefix}itc_tienda_carro.id, 
        {$wpdb->prefix}itc_tienda_carro.fecha, 
        {$wpdb->prefix}itc_tienda_carro_estado.nombre as estado, 
        {$wpdb->prefix}posts.post_title, 
        {$wpdb->prefix}posts.post_name,
        {$wpdb->prefix}itc_tienda_carro_detalle.cantidad,
        {$wpdb->prefix}itc_tienda_carro_detalle.producto_id,
        {$wpdb->prefix}itc_tienda_carro.direccion,
        {$wpdb->prefix}itc_tienda_carro.observaciones,
        {$wpdb->prefix}itc_tienda_carro.telefono,
        {$wpdb->prefix}itc_tienda_carro.ciudad,
        {$wpdb->prefix}itc_tienda_carro.tipo_pago
        from 
        {$wpdb->prefix}itc_tienda_carro_detalle 
        inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id 
        inner join {$wpdb->prefix}itc_tienda_carro_estado on {$wpdb->prefix}itc_tienda_carro_estado.id={$wpdb->prefix}itc_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}itc_tienda_carro_detalle.producto_id 
        where 
        {$wpdb->prefix}itc_tienda_carro.usuario_id='".$userdata->ID."' 
        and 
        {$wpdb->prefix}itc_tienda_carro.estado_id in (1,6);
        "); 

        // A partir de aquí comienza el HTML de la página de checkout
        ?>
        <div class="container">
        <main>
            <div class="py-5 text-center">
            <!-- Imagen y encabezado -->
            <img class="d-block mx-auto mb-4" src="<?php echo get_template_directory_uri() ?>/assets/images/checkout.jpeg" alt="" width="72" height="57">
            <h2>Formulario de pago</h2>
            <p class="lead">Para ir a pagar necesitamos algunos datos tuyos para poder realizar el envío...</p>
            <?php 
            // Si se detecta error de carrito vacío
            if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='2'){
                ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Una lástima!!</strong> Se quitaron todos los productos que tenías agregados al carrito.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
                <?php
            }
            ?>
            </div>
            <div class="row g-5">
                <div class="col-md-5 col-lg-4 order-md-last">
                <!-- Detalle de la compra -->
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Tu<?php echo (sizeof($compras)>1) ? 's':''?> compra<?php echo (sizeof($compras)>1) ? 's':''?></span><span class="badge bg-primary rounded-pill"><?php echo sizeof($compras);?></span>
                </h4>
                <ul class="list-group mb-3">
                <?php
                $sum=0; // Inicializa total
                foreach($compras as $compra){
                    // Suma precios x cantidad
                    $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
                    ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                    <!-- Enlace al producto -->
                    <a href="<?php echo get_site_url()."/".$compra->post_name;?>" class="checkout_title" title="<?php echo $compra->post_title;?>">
                    <h6 class="my-0"><?php echo substr($compra->post_title, 0, 20) ?>...</h6>
                    </a>
                    </div>
                    <!-- Muestra precio y cantidad -->
                    <span>$<?php echo number_format(get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad, 0, '', '.');?>(<?php echo $compra->cantidad;?>)</span>
                </li>
                    <?php
                }
                ?>
                <!-- Total -->
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total</span>
                    <strong>$<?php echo number_format($sum, 0, '', '.');?></strong>
                </li>
                <?php 
                 if(sizeof($compras)>=1){
                    ?>
                <!-- Botón limpiar carrito -->
                <li class="list-group-item d-flex justify-content-between">
                    <span>Limpiar carrito</span>
                    <a href="javascript:void(0);" onclick="limpiar_carrito();" title="Limpiar carrito" class="btn btn-warning"><i class="fas fa-shopping-cart"></i></a>
                </li>
                    <?php
                 }   
                ?>
                </ul>
                <!-- Formulario oculto para limpiar carrito -->
                <form action="" name="itc_tienda_form_limpiar_carrito" method="POST">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
                    <input type="hidden" name="carro_id" value="<?php echo isset($compras[0]->id) ? $compras[0]->id : ''; ?>" id="carro_id" />
                    <input type="hidden" name="action" value="limpiar-carrito-in" />
                </form>
                  <!--/detalle de la compra-->
                </div> <!-- Cierra el div anterior (detalle de la compra) -->
                <div class="col-md-7 col-lg-8"> <!-- Contenedor de 7 columnas en md y 8 en lg -->
                    <!--formulario-->
                    <h4 class="mb-3">Dirección de Envio</h4> <!-- Título de la sección de envío -->
                    <form class="needs-validation" name="itc_tienda_checkout" method="POST"> <!-- Formulario con validación y nombre cambiado a itc_tienda_checkout -->
                    <div class="row g-3"> <!-- Fila con separación de 3 -->

                    <div class="col-sm-12"> <!-- Columna completa -->
                        <label for="nombre" class="form-label">Primer nombre</label> <!-- Etiqueta del campo -->
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo $userdata->first_name;?>" readonly="true" /> <!-- Campo nombre cargado desde usuario, solo lectura -->
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                    <label for="apellido" class="form-label">Apellidos</label> <!-- Etiqueta apellidos -->
                        <div class="input-group has-validation"> <!-- Grupo de input con validación -->
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellidos" value="<?php echo $userdata->last_name;?>" readonly="true" /> <!-- Campo apellidos cargado desde usuario, solo lectura -->
                        </div>
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                    <label for="email" class="form-label">Email <span class="text-muted"></span></label> <!-- Etiqueta email -->
                    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo $userdata->user_email;?>" value="<?php echo $userdata->user_email;?>" readonly="true" /> <!-- Campo email cargado desde usuario, solo lectura -->
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                    <label for="telefono" class="form-label">Teléfono</label> <!-- Etiqueta teléfono -->
                    <input type="email" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" value="<?php echo isset($compras[0]->telefono) ? $compras[0]->telefono : ''; ?>" /> <!-- Campo teléfono cargado desde compras -->
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                    <label for="direccion" class="form-label">Dirección</label> <!-- Etiqueta dirección -->
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Calle los Árboles 123" value="<?php echo isset($compras[0]->direccion) ? $compras[0]->direccion : ''; ?>" /> <!-- Campo dirección cargado desde compras -->
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                    <label for="direccion2" class="form-label">Dirección 2 <span class="text-muted">(Optional)</span></label> <!-- Etiqueta dirección opcional -->
                    <input type="text" class="form-control" id="direccion2" name="direccion2" placeholder="Ej: A 100 mts de Colegio" /> <!-- Campo adicional de dirección -->
                    </div>

                    <div class="col-12"> <!-- Columna completa -->
                        <label for="observaciones" class="form-label">Observaciones</label> <!-- Etiqueta observaciones -->
                        <input type="text" class="form-control" id="observaciones" name="observaciones" placeholder="Ej: Lo recibirá mi mamá" value="<?php echo isset($compras[0]->observaciones) ? $compras[0]->observaciones : ''; ?>" /> <!-- Campo observaciones cargado desde compras -->
                    </div>

                    <div class="col-md-6"> <!-- Columna de 6 -->
                    <label for="pais" class="form-label">País</label> <!-- Etiqueta país -->
                    <select class="form-select" id="pais" name="pais"> <!-- Combo de países -->
                    <?php 
                    foreach($paises as $paise){ // Recorre todos los países
                        ?>
                        <option value="<?php echo $paise->nombre;?>" <?php echo ($paise->id==13)?'selected':'';?>><?php echo $paise->nombre;?></option> <!-- Muestra cada país, selecciona por defecto el id=13 -->
                        <?php
                    }
                    ?>
                    </select>
                    </div>

                    <div class="col-md-6"> <!-- Columna de 6 -->
                    <label for="ciudad" class="form-label">Ciudad</label> <!-- Etiqueta ciudad -->
                    <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad" value="<?php echo isset($compras[0]->ciudad) ? $compras[0]->ciudad : ''; ?>" /> <!-- Campo ciudad cargado desde compras -->
                    </div>

                    <div class="col-md-3"> <!-- Columna vacía para espaciado -->
                    </div>

                    </div> <!-- Cierra fila -->
                    <hr class="my-4"> <!-- Línea divisoria -->
                    <h4 class="mb-3">Pagar con</h4> <!-- Título sección pasarelas -->
                    <div class="my-3"> <!-- Contenedor de pasarelas -->
                        <?php 
                        foreach($pasarelas as $key=>$pasarela){ // Recorre las pasarelas
                            if(isset($compras[0]->tipo_pago) && $compras[0]->tipo_pago==$pasarela->id){ // Si ya hay tipo de pago guardado
                                $checked='checked="true"'; // Marca esa opción
                              }else{
                               $checked=($key==0)?'checked="true"':'';  // Si no, marca la primera opción
                              }
                            ?>
                        <div class="form-check"> <!-- Contenedor de opción -->
                            <input class="form-check-input" type="radio" name="pasarela" id="pasarela_<?php echo $key;?>" value="<?php echo $pasarela->id;?>" <?php echo $checked;?> /> <!-- Radio para elegir pasarela -->
                            <label for="pasarela_<?php echo $key;?>"><?php echo $pasarela->nombre;?></label> <!-- Nombre de la pasarela -->
                        </div>
                            <?php
                        }
                        ?>
                    </div>
                    <hr class="my-4"> <!-- Línea divisoria -->
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" /> <!-- Campo oculto con nonce de seguridad -->
                    <input type="hidden" name="carro_id" value="<?php echo isset($compras[0]->id) ? $compras[0]->id : ''; ?>" id="carro_id" /> <!-- Campo oculto con ID de compra -->
                    <input type="hidden" name="action" value="checkout-in" /> <!-- Acción a ejecutar en el backend -->
                    <?php 
                    if(sizeof($compras)>=1){ // Si existe al menos una compra
                        ?>
                        <button class="w-100 btn btn-success btn-lg" type="button" title="Pagar" onclick="itc_tienda_checkout_submit();"><i class="fas fa-arrow-right"></i> Pagar</button> <!-- Botón pagar, ejecuta función itc_tienda_checkout_submit() -->
                         <?php
                    }
                    ?>
                    </form> <!-- Cierre formulario -->
                    <!--/formulario-->
                </div>
            </div>
        </main>
        </div>
        <?php
    }

}
