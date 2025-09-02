<?php 
// Evita que se acceda directamente al archivo
if(!defined('ABSPATH')) die();

// Incluye los archivos de integración con cada pasarela de pago
//require_once plugin_dir_path( __FILE__ ) . 'webpay.php';
require_once plugin_dir_path( __FILE__ ) . 'paypal.php';
require_once plugin_dir_path( __FILE__ ) . 'mercado_pago.php';
//require_once plugin_dir_path( __FILE__ ) . 'stripe.php';

// Hook que se ejecuta después de que el tema se ha configurado
add_action('after_setup_theme', function(){
    // ---------------- WEBPAY ----------------
    /*
    if(isset($_GET['token_ws'])){
        $respuesta=tamila_tienda_webpay_verificar(sanitize_text_field( $_GET['token_ws'] ));
        if($respuesta['estado']==false){
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }else{
            global $wpdb;
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}ITC_tienda_carro where token ='".sanitize_text_field($_GET['token_ws'])."'");
         
            $wpdb->query("update 
                {$wpdb->prefix}tamila_tienda_carro 
                set estado_id=5,
                fecha_final=now()
                where token ='".sanitize_text_field($_GET['token_ws'])."';");
            
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }
    }
    */

    // ---------------- PAYPAL ----------------
    if(isset($_GET['PayerID']) and isset($_GET['token'])){
        global $wpdb;
        if(itc_tienda_paypal_captura(sanitize_text_field( $_GET['token'] ))=='COMPLETED'){
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where token ='".sanitize_text_field($_GET['token'])."'");
            $wpdb->query("update 
                {$wpdb->prefix}itc_tienda_carro 
                set estado_id=5,
                fecha_final=now()
                where token ='".sanitize_text_field($_GET['token'])."';");
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }else{
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }
    }
    if(!isset($_GET['PayerID']) and isset($_GET['token'])){
        wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
    }

    // ---------------- MERCADO PAGO ----------------
    if(isset($_GET['collection_id']) and isset($_GET['collection_status']) and isset($_GET['payment_id']) and isset($_GET['preference_id'])){
        global $wpdb;
        $status=sanitize_text_field($_GET['collection_status']);
        if($status=='approved'){
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where token ='".sanitize_text_field($_GET['preference_id'])."'");
            $wpdb->query("update 
            {$wpdb->prefix}itc_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where token ='".sanitize_text_field($_GET['preference_id'])."';");
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }else{
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }
    }

    // ---------------- STRIPE ----------------
    /*
    if(isset($_GET['stripe']) ){
        global $wpdb;
        $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where id ='".sanitize_text_field($_GET['stripe'])."'");
        if(sizeof($datos)==0){
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }else{
            $wpdb->query("update 
            {$wpdb->prefix}itc_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where id ='".sanitize_text_field($_GET['stripe'])."';");
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }
    }
    */
});

// ---------------- SHORTCODE ----------------
add_action('init', function(){
    add_shortcode( 'itc_tienda_verificacion', 'itc_tienda_verificacion_codigo_corto_display' );
});

// Función del shortcode
if(!function_exists('itc_tienda_verificacion_codigo_corto_display')){
    function itc_tienda_verificacion_codigo_corto_display($argumentos, $content=""){
        $userdata=wp_get_current_user();
        global $wpdb;

        $compras=$wpdb->get_results("select 
        {$wpdb->prefix}itc_tienda_carro.id, 
        {$wpdb->prefix}itc_tienda_carro.estado_id,
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
        {$wpdb->prefix}itc_tienda_carro.tipo_pago,
        {$wpdb->prefix}itc_tienda_carro_pasarelas.nombre as pasarela
        from 
        {$wpdb->prefix}itc_tienda_carro_detalle 
        inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id 
        inner join {$wpdb->prefix}itc_tienda_carro_estado on {$wpdb->prefix}itc_tienda_carro_estado.id={$wpdb->prefix}itc_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}itc_tienda_carro_detalle.producto_id 
        inner join {$wpdb->prefix}itc_tienda_carro_pasarelas on {$wpdb->prefix}itc_tienda_carro_pasarelas.id={$wpdb->prefix}itc_tienda_carro.tipo_pago
        where 
        {$wpdb->prefix}itc_tienda_carro.usuario_id='".$userdata->ID."' 
        and 
        {$wpdb->prefix}itc_tienda_carro.estado_id in (1, 6);
        ");  
        ?>
<div class="container">
    <main>
        <div class="py-5 text-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a  href="<?php echo get_site_url();?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo get_site_url();?>/checkout">Checkout</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verificación del pago</li>
                </ol>
            </nav>
            <hr/>
            <img class="d-block mx-auto mb-4 d-none d-md-block" src="<?php echo get_template_directory_uri() ?>/assets/images/checkout.jpeg" alt="" width="72" height="57">
            <h2 class="card-title mt-2">Verificación del pago</h2>

            <?php 
            if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){ ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Ups</strong> Ocurrió un error con el pago, por favor vuelve a intentarlo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <?php if(isset( $_REQUEST["error"] ) and $_REQUEST['error']=='2'){ ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Excelente</strong> Se realizó el pago exitosamente con la Orden de compra N° <?php echo $_GET['id']?>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
        </div>

        <?php if(sizeof($compras)>=1){ ?>
        <div class="row">
            <div class="col-12">
                <h4>Datos para el pago</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Dato</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Orden de compra N°</td>
                                <td><?php echo $compras[0]->id?></td>
                            </tr>
                            <tr>
                                <td>Método de pago</td>
                                <td><?php echo $compras[0]->pasarela?></td>
                            </tr>
                            <tr>
                                <td>Nombre</td>
                                <td><?php echo $userdata->user_firstname." ".$userdata->user_lastname?></td>
                            </tr>
                            <tr>
                                <td>E-Mail</td>
                                <td><?php echo $userdata->user_email?></td>
                            </tr>
                            <tr>
                                <td>Teléfono</td>
                                <td><?php echo $compras[0]->telefono?></td>
                            </tr>
                            <tr>
                                <td>Dirección</td>
                                <td><?php echo $compras[0]->direccion?></td>
                            </tr>
                            <tr>
                                <td>Ciudad</td>
                                <td><?php echo $compras[0]->ciudad?></td>
                            </tr>
                            <tr>
                                <td>Observaciones</td>
                                <td><?php echo $compras[0]->observaciones?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4>Productos</h4>
<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="d-none d-md-table-cell">Foto</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $sum=0;
        foreach($compras as $c){
            $sum+=$precio=get_post_meta($c->producto_id,'precio',true)*$c->cantidad; ?>
            <tr>
                <td class="text-center d-none d-md-table-cell">
                    <a href="<?php echo get_site_url()."/".$c->post_name;?>" title="<?php echo $c->post_title;?>">
                        <img class="img-fluid foto-mis-compras" src="<?php echo get_the_post_thumbnail_url($c->producto_id,'thumbnail');?>" alt="<?php echo $c->post_title;?>" />
                    </a>
                </td>
                <td><a href="<?php echo get_site_url()."/".$c->post_name;?>" class="checkout_title" title="<?php echo $c->post_title;?>"><?php echo $c->post_title;?></a></td>
                <td>$<?php echo number_format(get_post_meta($c->producto_id,'precio',true),0,'','.');?></td>
                <td><?php echo $c->cantidad;?></td>
                <td>$<?php echo number_format(get_post_meta($c->producto_id,'precio',true)*$c->cantidad,0,'','.');?></td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td><strong>$<?php echo number_format($sum,0,'','.');?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- VERSION MOBILE -->
<div class="d-md-none">
<?php foreach($compras as $c){ ?>
    <div class="card mb-2 p-2">
        <div class="d-flex justify-content-between">
            <strong>Producto:</strong> <?php echo $c->post_title;?>
        </div>
        <div class="d-flex justify-content-between">
            <span>Precio: $<?php echo number_format(get_post_meta($c->producto_id,'precio',true),0,'','.');?></span>
            <span>Cantidad: <?php echo $c->cantidad;?></span>
        </div>
        <div class="d-flex justify-content-between">
            <strong>Total: $<?php echo number_format(get_post_meta($c->producto_id,'precio',true)*$c->cantidad,0,'','.');?></strong>
        </div>
    </div>
<?php } ?>
<div class="d-flex justify-content-end mt-2">
    <strong>Total general: $<?php echo number_format($sum,0,'','.');?></strong>
</div>
</div>


                <div class="d-flex justify-content-between mt-3">
                    <a href="<?php echo get_site_url()."/checkout"?>" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Volver al checkout</a>
                    <?php 
                    switch($compras[0]->tipo_pago){
                        case '2':
                            $pagar=itc_tienda_paypal_token($compras);
                            $wpdb->query("update {$wpdb->prefix}itc_tienda_carro set monto='".$sum."', token='".$pagar['token']."' where id='".$compras[0]->id."'");
                            ?>
                            <a href="<?php echo $pagar['url']?>" class="btn btn-warning"><i class="fab fa-paypal"></i> Pagar con <?php echo $compras[0]->pasarela?></a>
                            <?php
                        break;
                        case '3':
                            $pagar=itc_tienda_mercado_pago_token($compras);
                            $wpdb->query("update {$wpdb->prefix}itc_tienda_carro set monto='".$sum."', token='".$pagar['token']."' where id='".$compras[0]->id."'");
                            ?>
                            <a href="<?php echo $pagar['url']?>" class="btn btn-warning"><i class="far fa-handshake"></i> Pagar con <?php echo $compras[0]->pasarela?></a>
                            <?php
                        break;
                    }
                    ?>
                </div>

            </div>
        </div>
        <?php } ?>
    </main>
</div>
<?php
    }
}
