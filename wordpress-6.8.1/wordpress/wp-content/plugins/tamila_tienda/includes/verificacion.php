<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'webpay.php';
require_once plugin_dir_path( __FILE__ ) . 'paypal.php';
require_once plugin_dir_path( __FILE__ ) . 'mercado_pago.php';
require_once plugin_dir_path( __FILE__ ) . 'stripe.php';
add_action('after_setup_theme', function(){
  
    //webpay
    if(isset($_GET['token_ws'])){
        $respuesta=tamila_tienda_webpay_verificar(sanitize_text_field( $_GET['token_ws'] ));
        if($respuesta['estado']==false){
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }else{
            global $wpdb;
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}tamila_tienda_carro where token ='".sanitize_text_field($_GET['token_ws'])."'");
         
            $wpdb->query("update 
                {$wpdb->prefix}tamila_tienda_carro 
                set estado_id=5,
                fecha_final=now()
                where token ='".sanitize_text_field($_GET['token_ws'])."';");
            
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }
    }
    //paypal
    if(isset($_GET['PayerID']) and isset($_GET['token'])){
        global $wpdb;
        if(tamila_tienda_paypal_captura(sanitize_text_field( $_GET['token'] ))=='COMPLETED'){
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}tamila_tienda_carro where token ='".sanitize_text_field($_GET['token'])."'");
            $wpdb->query("update 
                {$wpdb->prefix}tamila_tienda_carro 
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
    //mercado pago
    //?collection_id=60574408077&collection_status=approved&payment_id=60574408077&status=approved&external_reference=null&payment_type=debit_card&merchant_order_id=10413205180&preference_id=1249226080-57c5be47-a95c-4df5-ae77-3d03c376a452&site_id=MLC&processing_mode=aggregator&merchant_account_id=null
    if(isset($_GET['collection_id'])   and isset($_GET['collection_status']) and isset($_GET['payment_id']) and isset($_GET['preference_id'])){
        global $wpdb;
        $status=sanitize_text_field($_GET['collection_status']);
        if($status=='approved'){
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}tamila_tienda_carro where token ='".sanitize_text_field($_GET['preference_id'])."'");
            $wpdb->query("update 
            {$wpdb->prefix}tamila_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where token ='".sanitize_text_field($_GET['preference_id'])."';");
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }else{
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }
    }

    //stripe
    if(isset($_GET['stripe']) ){
        global $wpdb;
        $datos=$wpdb->get_results("select id from {$wpdb->prefix}tamila_tienda_carro where id ='".sanitize_text_field($_GET['stripe'])."'");
        if(sizeof($datos)==0){
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }else{
            $wpdb->query("update 
            {$wpdb->prefix}tamila_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where id ='".sanitize_text_field($_GET['stripe'])."';");
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }
    }
});

//shortcode
//[tamila_tienda_verificacion id="1"]
add_action('init', function(){
    add_shortcode( 'tamila_tienda_verificacion', 'tamila_tienda_verificacion_codigo_corto_display' );
});
if(!function_exists('tamila_tienda_verificacion_codigo_corto_display')){
    function tamila_tienda_verificacion_codigo_corto_display($argumentos, $content=""){
        $userdata=wp_get_current_user();
        ///print_r($_REQUEST);exit;
        global $wpdb;
        $compras=$wpdb->get_results("select 
        {$wpdb->prefix}tamila_tienda_carro.id, 
        {$wpdb->prefix}tamila_tienda_carro.estado_id,
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
        {$wpdb->prefix}tamila_tienda_carro.tipo_pago,
        {$wpdb->prefix}tamila_tienda_carro_pasarelas.nombre as pasarela
        from 
        {$wpdb->prefix}tamila_tienda_carro_detalle 
        inner join {$wpdb->prefix}tamila_tienda_carro on {$wpdb->prefix}tamila_tienda_carro.id={$wpdb->prefix}tamila_tienda_carro_detalle.tamila_tienda_carro_id 
        inner join {$wpdb->prefix}tamila_tienda_carro_estado on {$wpdb->prefix}tamila_tienda_carro_estado.id={$wpdb->prefix}tamila_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}tamila_tienda_carro_detalle.producto_id 
        inner join {$wpdb->prefix}tamila_tienda_carro_pasarelas on {$wpdb->prefix}tamila_tienda_carro_pasarelas.id={$wpdb->prefix}tamila_tienda_carro.tipo_pago
        where 
        {$wpdb->prefix}tamila_tienda_carro.usuario_id='".$userdata->ID."' 
        and 
        {$wpdb->prefix}tamila_tienda_carro.estado_id in (1, 6);
        ");  
        ?>
    <div class="container">
        <main>
            <div class="py-5 text-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a  href="<?php echo get_site_url();?>">Home </a></li>
                    <li class="breadcrumb-item"><a href="<?php echo get_site_url();?>/checkout">Checkout</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verificación del pago </li>
                </ol>
            </nav><hr/>
            <img class="d-block mx-auto mb-4" src="<?php echo get_template_directory_uri() ?>/assets/img/checkout.jpeg" alt="" width="72" height="57">
            <h2 class="card-title mt-2">Verificación del pago </h2>
            <?php 
                        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){
                            ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Ups</strong> Ocurrió un error con el pago, por favor vuelve a intentarlo.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                            <?php
                        }
                        ?>
                        <?php 
                        
            if(isset( $_REQUEST["error"] ) and  $_REQUEST['error'] =='2'){
                
                ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Excelente</strong> Se realizó el pago exitosamente con la Orden de compra N° <?php echo $_GET['id']?>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
                <?php
            }
            ?>
            </div>
            <?php 
            if(sizeof($compras)>=1){
                ?>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                        
                         <h4>Datos para el pago</h4>
                        <!--datos del pago-->
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
                        <!--/datos del pago-->
                        </div>
                        <hr/>
                        <div class="col-12">
                        <h4>Productos</h4>
                        <!--productos-->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $sum=0;
                                foreach($compras as $compra){
                                    
                                    $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
                                    ?>
                                        <tr>
                                        <td style="text-align:center;">
                                <a href="<?php echo get_site_url()."/".$compra->post_name?>" title="<?php echo $compra->post_title ?>">
                                <img class="img-fluid foto-mis-compras" src="<?php echo  get_site_url().substr(wp_get_attachment_image_src( get_post_thumbnail_id($compra->producto_id), 'post')[0],strlen(get_site_url()), strlen(wp_get_attachment_image_src( get_post_thumbnail_id($compra->producto_id), 'post')[0])); ?>" alt="?php echo $compra->post_title ?>" />
                                </a>
                            </td>
                                        <td><a href="<?php echo get_site_url()."/".$compra->post_name;?>" class="checkout_title" title="<?php echo $compra->post_title;?>"> <?php echo $compra->post_title;?></a>
                                        </td>  
                                        <td>$<?php echo number_format( get_post_meta( $compra->producto_id, 'precio' )[0], 0, '', '.');?></td> 
                                        <td><?php echo $compra->cantidad;?></td> 
                                        <td>$<?php echo number_format( get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad, 0, '', '.')?></td>  
                                    </tr>
                                    <?php 
                                }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>Total:</strong></td>
                                        <td  ><strong>$<?php echo number_format($sum,0,'','.');?></strong> </td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                        <!--/productos-->
                        <hr/>

                        </div>
                        <div class="col-6">
                        <a href="<?php echo get_site_url()."/checkout"?>" class="btn btn-warning" title="Volver al checkout"><i class="fas fa-arrow-left"></i> Volver al checkout</a>
                        </div>
                        <div class="col-6">
                        <?php 
                        switch($compras[0]->tipo_pago){
                            case '1':
                                $pagar=tamila_tienda_webpay_token($compras);
                                $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'");
                                ?>
                                <a href="javascript:void(0);" class="btn btn-danger" title="Pagar con <?php echo $compras[0]->pasarela?>" onclick="document.tamila_tienda_form_webpay.submit();">
                                <i class="fas fa-dollar-sign"></i> Pagar con <?php echo $compras[0]->pasarela?>
                                </a>
                                <form action="<?php echo $pagar['url']?>" method="POST" name="tamila_tienda_form_webpay">
                                <input type="hidden" name="token_ws" id="token_ws" value="<?php echo $pagar['token']?>"  />
                                
                                </form>
                                <?php
                            break;
                            case '2':
                                $pagar=tamila_tienda_paypal_token($compras);
                                $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'");
                                ?>
                                <a href="<?php echo $pagar['url']?>" class="btn btn-warning" title="Pagar con <?php echo $compras[0]->pasarela?>">
                                <i class="fab fa-paypal"></i> Pagar con <?php echo $compras[0]->pasarela?>
                                </a>
                                <?php

                            break;
                            case '3':
                                $pagar=tamila_tienda_mercado_pago_token($compras);
                                $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'");
                                ?>
                                <a href="<?php echo $pagar['url']?>" class="btn btn-warning" title="Pagar con <?php echo $compras[0]->pasarela?>">
                                <i class="far fa-handshake"></i> Pagar con <?php echo $compras[0]->pasarela?>
                                </a>
                                <?php
                            break;
                            case '4':
                                $pagar=tamila_tienda_stripe_obtener_token($compras);
                                $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'");
                                ?>
                                <a href="<?php echo $pagar['url']?>" class="btn btn-info" title="Pagar con <?php echo $compras[0]->pasarela?>">
                                <i class="fab fa-stripe"></i> Pagar con <?php echo $compras[0]->pasarela?>
                                </a>
                                <?php
                            break;
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
                <?php
            }
            ?>
            
        </main>
    </div>
        <?php
    }
}