<?php 
if(!defined('ABSPATH')) die();


add_action('after_setup_theme', function(){
    global $wpdb;
    if(isset($_POST['nonce']) and $_POST['action']=='limpiar-carrito-in'){
        $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro set estado_id=2 where id='".sanitize_text_field($_POST['carro_id'])."';");
        wp_safe_redirect( home_url('checkout')."?error=2" ); exit;
    }
    if(isset($_POST['nonce']) and $_POST['action']=='checkout-in'){
        $sql="update {$wpdb->prefix}tamila_tienda_carro 
        set 
        estado_id=6, 
        direccion='".sanitize_text_field($_POST['direccion'])." ".sanitize_text_field($_POST['direccion2'])."', 
        telefono='".sanitize_text_field($_POST['telefono'])."', 
        observaciones='".sanitize_text_field($_POST['observaciones'])."',
         tipo_pago='".sanitize_text_field($_POST['pasarela'])."', 
         ciudad='".sanitize_text_field($_POST['ciudad'])."' 
         where 
         id='".sanitize_text_field($_POST['carro_id'])."';";
         $wpdb->query($sql);
         wp_safe_redirect( home_url('verificacion') ); exit;
    }
});

//[tamila_tienda_checkout id="1"]
add_action('init', function(){
    add_shortcode( 'tamila_tienda_checkout', 'tamila_tienda_checkout_codigo_corto_display' );
});


if(!function_exists('tamila_tienda_checkout_codigo_corto_display')){
    function tamila_tienda_checkout_codigo_corto_display($argumentos, $content=""){
        if ( !is_user_logged_in() ) {
            echo '<script>window.location="'.get_site_url().'";</script>';exit;
        }
        $userdata=wp_get_current_user();
        global $wpdb;
        $paises=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_pais;");
        $pasarelas=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where estado_id=1;");
        $compras=$wpdb->get_results("select 
        {$wpdb->prefix}tamila_tienda_carro.id, 
        {$wpdb->prefix}tamila_tienda_carro.fecha, 
        {$wpdb->prefix}tamila_tienda_carro_estado.nombre as estado, 
       
        {$wpdb->prefix}posts.post_title, 
        {$wpdb->prefix}posts.post_name,
        {$wpdb->prefix}tamila_tienda_carro_detalle.cantidad,
        {$wpdb->prefix}tamila_tienda_carro_detalle.producto_id,
        {$wpdb->prefix}tamila_tienda_carro.direccion,
        {$wpdb->prefix}tamila_tienda_carro.observaciones,
        {$wpdb->prefix}tamila_tienda_carro.telefono,
        {$wpdb->prefix}tamila_tienda_carro.ciudad,
        {$wpdb->prefix}tamila_tienda_carro.tipo_pago
        from 
        {$wpdb->prefix}tamila_tienda_carro_detalle 
        inner join {$wpdb->prefix}tamila_tienda_carro on {$wpdb->prefix}tamila_tienda_carro.id={$wpdb->prefix}tamila_tienda_carro_detalle.tamila_tienda_carro_id 
        inner join {$wpdb->prefix}tamila_tienda_carro_estado on {$wpdb->prefix}tamila_tienda_carro_estado.id={$wpdb->prefix}tamila_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}tamila_tienda_carro_detalle.producto_id 
        where 
        {$wpdb->prefix}tamila_tienda_carro.usuario_id='".$userdata->ID."' 
        and 
        {$wpdb->prefix}tamila_tienda_carro.estado_id in (1,6);
        "); 
        ?>
        <div class="container">
        <main>
            <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="<?php echo get_template_directory_uri() ?>/assets/img/checkout.jpeg" alt="" width="72" height="57">
            <h2>Formulario de pago</h2>
            <p class="lead">Para ir a pagar necesitamos algunos datos tuyos para poder realizar el envío de los productos. <strong>Es muy importante que indiques de forma correcta los datos de tu dirección de envío, para que tu pedido te llegue de manera correcta y se eviten retrasos.</strong></p>
            <?php 
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
                <!--detalle de la compra-->
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Tu<?php echo (sizeof($compras)>1) ? 's':''?> compra<?php echo (sizeof($compras)>1) ? 's':''?></span><span class="badge bg-primary rounded-pill"><?php echo sizeof($compras);?></span>
                </h4>
                <ul class="list-group mb-3">
                <?php
                $sum=0; 
                foreach($compras as $compra){
                    $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
                    ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                    <a href="<?php echo get_site_url()."/".$compra->post_name;?>" class="checkout_title" title="<?php echo $compra->post_title;?>">
                    <h6 class="my-0"><?php echo substr($compra->post_title, 0, 20) ?>...</h6>
                    </a>
                    </div>
                    <span>$<?php echo number_format(get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad, 0, '', '.');?>(<?php echo $compra->cantidad;?>)</span>
                </li>
                    <?php
                }
                ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total</span>
                    <strong>$<?php echo number_format($sum, 0, '', '.');?></strong>
                </li>
                <?php 
                 if(sizeof($compras)>=1){
                    ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Limpiar carrito</span>
                    <a href="javascript:void(0);" onclick="limpiar_carrito();" title="Limpiar carrito" class="btn btn-warning"><i class="fas fa-shopping-cart"></i></a>
                    
                </li>
                    <?php
                 }   
                ?>
                </ul>
                <form action="" name="tamila_tienda_form_limpiar_carrito" method="POST">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
                    <input type="hidden" name="carro_id" value="<?php echo $compras[0]->id;?>" id="carro_id" />
                    <input type="hidden" name="action" value="limpiar-carrito-in" />
                    </form>
                <!--/detalle de la compra-->
                </div>
                <div class="col-md-7 col-lg-8">
                    <!--formulario-->
                    <h4 class="mb-3">Dirección de Envio</h4>
                    <form class="needs-validation" name="tamila_tienda_checkout" method="POST">
                    <div class="row g-3">
                    <div class="col-sm-12">
                        <label for="nombre" class="form-label">Primer nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo $userdata->first_name;?>" readonly="true" />
                    
                    </div>
                    <div class="col-12">
                    <label for="apellido" class="form-label">Apellidos</label>
                        <div class="input-group has-validation">
                    
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellidos" value="<?php echo $userdata->last_name;?>" readonly="true" />
                    
                        </div>
                    </div>
                    <div class="col-12">
                    <label for="email" class="form-label">Email <span class="text-muted"></span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo $userdata->user_email;?>" value="<?php echo $userdata->user_email;?>" readonly="true" />
                
                    </div>

                    <div class="col-12">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="email" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" value="<?php echo $compras[0]->telefono?>" />
                
                    </div>

                    <div class="col-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Calle los Árboles 123" value="<?php echo $compras[0]->direccion?>" />
                
                    </div>

                    <div class="col-12">
                    <label for="direccion2" class="form-label">Dirección 2 <span class="text-muted">(Optional)</span></label>
                    <input type="text" class="form-control" id="direccion2" name="direccion2" placeholder="Ej: A 100 mts de Colegio" />
                    </div>

                    <div class="col-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <input type="text" class="form-control" id="observaciones" name="observaciones" placeholder="Ej: Lo recibirá mi mamá" value="<?php echo $compras[0]->observaciones?>" />
                    
                    </div>

                    <div class="col-md-6">
                    <label for="pais" class="form-label">País</label>
                    <select class="form-select" id="pais" name="pais">
                    <?php 
                    foreach($paises as $paise){
                        ?>
                        <option value="<?php echo $paise->nombre;?>" <?php echo ($paise->id==46)?'selected':'';?>><?php echo $paise->nombre;?></option>
                        <?php
                    }
                    ?>
                    
                    </select>
                    
                    </div>
                    
                    <div class="col-md-6">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad" value="<?php echo $compras[0]->ciudad?>" />
                
                    </div>

                    <div class="col-md-3">
                    </div>


                    </div>
                    <hr class="my-4">
                    <h4 class="mb-3">Pagar con</h4>
                    <div class="my-3">
                        <?php 
                        foreach($pasarelas as $key=>$pasarela){
                            if(isset($compras[0]->tipo_pago) and $compras[0]->tipo_pago==$pasarela->id){
                                $checked='checked="true"';
                              }else{
                               $checked=($key==0)?'checked="true"':''; 
                              }
                            ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pasarela" id="pasarela_<?php echo $key;?>" value="<?php echo $pasarela->id;?>" <?php echo $checked;?> />
                            <label for="pasarela_<?php echo $key;?>"><?php echo $pasarela->nombre;?></label>
                        </div>
                            <?php
                        }
                        ?>
                    </div>
                    <hr class="my-4">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
                    <input type="hidden" name="carro_id" value="<?php echo $compras[0]->id;?>" id="carro_id" />
                    <input type="hidden" name="action" value="checkout-in" />
                    <?php 
                    if(sizeof($compras)>=1){
                        ?>
                        <button class="w-100 btn btn-success btn-lg" type="button" title="Pagar" onclick="tamila_tienda_checkout_submit();"><i class="fas fa-arrow-right"></i> Pagar</button>
                         <?php
                    }
                    ?>
                    </form>
                    <!--/formulario-->
                </div>
            </div>
        </main>
        </div>
        <?php
    }
}