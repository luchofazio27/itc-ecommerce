<?php 
if(!defined('ABSPATH')) die();

add_action('after_setup_theme', function(){
    global $wpdb;

    // Limpiar carrito
    if(isset($_POST['nonce']) and $_POST['action']=='limpiar-carrito-in'){
        $wpdb->query("update {$wpdb->prefix}itc_tienda_carro set estado_id=2 where id='".sanitize_text_field($_POST['carro_id'])."';");
        wp_safe_redirect( home_url('checkout')."?error=2" ); exit;
    }

    // Checkout
    if(isset($_POST['nonce']) and $_POST['action']=='checkout-in'){
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
        $wpdb->query($sql); 
        wp_safe_redirect( home_url('verificacion') ); exit;
    }
});

add_action('init', function(){
    add_shortcode( 'itc_tienda_checkout', 'itc_tienda_checkout_codigo_corto_display' );
});

if(!function_exists('itc_tienda_checkout_codigo_corto_display')){
    function itc_tienda_checkout_codigo_corto_display($argumentos, $content=""){
        if(!is_user_logged_in()){
            echo '<script>window.location="'.get_site_url().'";</script>';exit;
        }

        $userdata = wp_get_current_user();
        global $wpdb;

        $paises = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}itc_tienda_pais;");
        $pasarelas = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}itc_tienda_carro_pasarelas WHERE estado_id=1;");
        $compras = $wpdb->get_results("SELECT 
            c.id, c.fecha, ce.nombre as estado, p.post_title, p.post_name, d.cantidad, d.producto_id,
            c.direccion, c.observaciones, c.telefono, c.ciudad, c.tipo_pago
            FROM {$wpdb->prefix}itc_tienda_carro_detalle d
            INNER JOIN {$wpdb->prefix}itc_tienda_carro c ON c.id=d.itc_tienda_carro_id
            INNER JOIN {$wpdb->prefix}itc_tienda_carro_estado ce ON ce.id=c.estado_id
            INNER JOIN {$wpdb->prefix}posts p ON p.ID=d.producto_id
            WHERE c.usuario_id='".$userdata->ID."' AND c.estado_id IN (1,6);"
        ); 

        ?>
        <div class="container">
            <main>
                <div class="py-5 text-center">
                    <img class="d-block mx-auto mb-4" src="<?php echo get_template_directory_uri() ?>/assets/images/checkout.jpeg" alt="" width="72" height="57">
                    <h2>Formulario de pago</h2>
                    <p class="lead">Para ir a pagar necesitamos algunos datos tuyos para poder realizar el envío...</p>
                    <?php if(isset($_REQUEST["error"]) and sanitize_text_field($_REQUEST['error'])=='2'){ ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Una lástima!!</strong> Se quitaron todos los productos que tenías agregados al carrito.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php } ?>
                </div>

                <div class="row g-4">
                    <!-- Formulario de envío a la izquierda -->
                    <div class="col-md-7 order-md-1">
                        <form class="needs-validation itc-form-container" name="itc_tienda_checkout" method="POST">
                            <h4 class="mb-3">Dirección de Envío</h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="nombre" class="form-label">Primer nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $userdata->first_name;?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label for="apellido" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $userdata->last_name;?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $userdata->user_email;?>" readonly>
                                </div>
                                <div class="col-12">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo isset($compras[0]->telefono) ? $compras[0]->telefono : ''; ?>">
                                </div>
                                <div class="col-12">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo isset($compras[0]->direccion) ? $compras[0]->direccion : ''; ?>">
                                </div>
                                <div class="col-12">
                                    <label for="direccion2" class="form-label">Dirección 2 <span class="text-muted">(Opcional)</span></label>
                                    <input type="text" class="form-control" id="direccion2" name="direccion2">
                                </div>
                                <div class="col-12">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <input type="text" class="form-control" id="observaciones" name="observaciones" value="<?php echo isset($compras[0]->observaciones) ? $compras[0]->observaciones : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="pais" class="form-label">País</label>
                                    <select class="form-select" id="pais" name="pais">
                                        <?php foreach($paises as $paise){ ?>
                                            <option value="<?php echo $paise->nombre;?>" <?php echo ($paise->id==13)?'selected':'';?>><?php echo $paise->nombre;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo isset($compras[0]->ciudad) ? $compras[0]->ciudad : ''; ?>">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h4 class="mb-3">Pagar con</h4>
                            <div class="my-3">
                                <?php foreach($pasarelas as $key=>$pasarela){
                                    $checked=(isset($compras[0]->tipo_pago) && $compras[0]->tipo_pago==$pasarela->id)?'checked':'';
                                ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pasarela" id="pasarela_<?php echo $key;?>" value="<?php echo $pasarela->id;?>" <?php echo $checked;?>>
                                        <label for="pasarela_<?php echo $key;?>"><?php echo $pasarela->nombre;?></label>
                                    </div>
                                <?php } ?>
                            </div>

                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>">
                            <input type="hidden" name="carro_id" value="<?php echo isset($compras[0]->id) ? $compras[0]->id : ''; ?>">
                            <input type="hidden" name="action" value="checkout-in">

                            <?php if(sizeof($compras)>=1){ ?>
                                <button class="w-100 btn btn-success btn-lg" type="button" onclick="itc_tienda_checkout_submit();"><i class="fas fa-arrow-right"></i> Pagar</button>
                            <?php } ?>
                        </form>
                    </div>

                    <!-- Detalle de compras a la derecha -->
                    <div class="col-md-5 order-md-2">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-primary">Tus compras</span>
                            <span class="badge bg-primary rounded-pill"><?php echo sizeof($compras);?></span>
                        </h4>
                        <ul class="list-group mb-3">
                            <?php
                            $sum=0;
                            foreach($compras as $compra){
                                $precio=get_post_meta($compra->producto_id,'precio')[0]*$compra->cantidad;
                                $sum+=$precio;
                            ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <a href="<?php echo get_site_url()."/".$compra->post_name;?>" class="checkout_title" title="<?php echo $compra->post_title;?>">
                                            <h6 class="my-0"><?php echo substr($compra->post_title,0,20); ?>...</h6>
                                        </a>
                                    </div>
                                    <span>$<?php echo number_format($precio,0,'','.');?>(<?php echo $compra->cantidad;?>)</span>
                                </li>
                            <?php } ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total</span>
                                <strong>$<?php echo number_format($sum,0,'','.');?></strong>
                            </li>
                            <?php if(sizeof($compras)>=1){ ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Limpiar carrito</span>
                                <a href="javascript:void(0);" onclick="limpiar_carrito();" class="btn btn-warning"><i class="fas fa-shopping-cart"></i></a>
                            </li>
                            <?php } ?>
                        </ul>

                        <form action="" name="itc_tienda_form_limpiar_carrito" method="POST">
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>">
                            <input type="hidden" name="carro_id" value="<?php echo isset($compras[0]->id) ? $compras[0]->id : ''; ?>">
                            <input type="hidden" name="action" value="limpiar-carrito-in">
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <style>
        .itc-form-container {
            background:#fff;
            border-radius:10px;
            padding:25px;
            box-shadow:0 5px 15px rgba(0,0,0,0.08);
        }
        .itc-form-container h4 {color:#333; font-weight:600; text-align:left;}
        .itc-form-container label {color:#444; font-weight:500;}
        .itc-form-container input.form-control, .itc-form-container select.form-select {
            border-radius:6px; border:1px solid #ddd; padding:10px; font-size:1rem;
        }
        .itc-form-container input.form-control:focus, .itc-form-container select.form-select:focus {
            border-color:#ffc107; box-shadow:0 0 6px rgba(255,193,7,0.4); outline:none;
        }
        .itc-form-container .btn {display:block; width:100%; padding:12px; font-weight:600; border-radius:6px;}
        .itc-form-container .btn-success:hover {background-color:#198754cc; border-color:#198754;}
        </style>
        <?php
    }
}
