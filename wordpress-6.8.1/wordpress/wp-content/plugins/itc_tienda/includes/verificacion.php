<?php 
// Evita que se acceda directamente al archivo
if(!defined('ABSPATH')) die();

// Incluye los archivos de integración con cada pasarela de pago
require_once plugin_dir_path( __FILE__ ) . 'paypal.php';
//require_once plugin_dir_path( __FILE__ ) . 'mercado_pago.php';
//require_once plugin_dir_path( __FILE__ ) . 'stripe.php';

// Hook que se ejecuta después de que el tema se ha configurado
add_action('after_setup_theme', function(){
  

    // ---------------- PAYPAL ----------------
    if(isset($_GET['PayerID']) and isset($_GET['token'])){ // Si viene info de PayPal
        global $wpdb;
        // Verifica el pago con PayPal
        if(itc_tienda_paypal_captura(sanitize_text_field( $_GET['token'] ))=='COMPLETED'){
            // Busca el carro asociado al token
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where token ='".sanitize_text_field($_GET['token'])."'");
            // Marca el carro como pagado
            $wpdb->query("update 
                {$wpdb->prefix}itc_tienda_carro 
                set estado_id=5,
                fecha_final=now()
                where token ='".sanitize_text_field($_GET['token'])."';");
            // Redirige a verificación con éxito
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }else{
            // Redirige con error
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }
    }
    // Si falta PayerID pero viene token, también es error
    if(!isset($_GET['PayerID']) and isset($_GET['token'])){
        wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
    }

    // ---------------- MERCADO PAGO ----------------
    if(isset($_GET['collection_id']) and isset($_GET['collection_status']) and isset($_GET['payment_id']) and isset($_GET['preference_id'])){
        global $wpdb;
        $status=sanitize_text_field($_GET['collection_status']); // Estado del pago
        if($status=='approved'){ // Si fue aprobado
            // Busca el carro asociado al token
            $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where token ='".sanitize_text_field($_GET['preference_id'])."'");
            // Marca como pagado
            $wpdb->query("update 
            {$wpdb->prefix}itc_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where token ='".sanitize_text_field($_GET['preference_id'])."';");
            // Redirige a verificación exitosa
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }else{
            // Redirige con error
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }
    }

    // ---------------- STRIPE ----------------
    if(isset($_GET['stripe']) ){
        global $wpdb;
        // Busca el carro por ID
        $datos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where id ='".sanitize_text_field($_GET['stripe'])."'");
        if(sizeof($datos)==0){ // Si no existe el carro
            wp_safe_redirect( home_url('verificacion')."?error=1" ); exit;
        }else{
            // Marca como pagado
            $wpdb->query("update 
            {$wpdb->prefix}itc_tienda_carro 
            set estado_id=5,
            fecha_final=now()
            where id ='".sanitize_text_field($_GET['stripe'])."';");
            // Redirige con éxito
            wp_safe_redirect( home_url('verificacion')."?error=2&id=".$datos[0]->id  ); exit;
        }
    }
});

// ---------------- SHORTCODE ----------------
// Define el shortcode [itc_tienda_verificacion id="1"]
add_action('init', function(){
    add_shortcode( 'itc_tienda_verificacion', 'itc_tienda_verificacion_codigo_corto_display' );
});

// Función que genera el contenido del shortcode
if(!function_exists('itc_tienda_verificacion_codigo_corto_display')){
    function itc_tienda_verificacion_codigo_corto_display($argumentos, $content=""){
        $userdata=wp_get_current_user(); // Obtiene datos del usuario logueado
        global $wpdb;

        // Consulta las compras pendientes o en proceso del usuario
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
<div class="container"> <!-- Contenedor principal de la vista -->
        <main> <!-- Contenido principal de la página -->
            <div class="py-5 text-center"> <!-- Cabecera con padding y texto centrado -->
            <nav aria-label="breadcrumb"> <!-- Navegación tipo breadcrumb -->
                <ol class="breadcrumb"> <!-- Lista de migas de pan -->
                    <li class="breadcrumb-item"><a  href="<?php echo get_site_url();?>">Home </a></li> <!-- Enlace a la home -->
                    <li class="breadcrumb-item"><a href="<?php echo get_site_url();?>/checkout">Checkout</a></li> <!-- Enlace al checkout -->
                    <li class="breadcrumb-item active" aria-current="page">Verificación del pago </li> <!-- Paso activo: verificación -->
                </ol> <!-- Cierre de lista breadcrumb -->
            </nav><hr/> <!-- Cierre del breadcrumb y separador horizontal -->
            <img class="d-block mx-auto mb-4" src="<?php echo get_template_directory_uri() ?>/assets/images/checkout.jpeg" alt="" width="72" height="57"> <!-- Imagen decorativa del checkout -->
            <h2 class="card-title mt-2">Verificación del pago </h2> <!-- Título de la sección -->

            <?php 
                        if(isset( $_REQUEST["error"] ) and sanitize_text_field( $_REQUEST['error'] )=='1'){ // Si hay parámetro error=1 (falló el pago)
                            ?> <!-- Cierra PHP para imprimir HTML -->
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"> <!-- Alerta de error de pago -->
                        <strong>Ups</strong> Ocurrió un error con el pago, por favor vuelve a intentarlo. <!-- Mensaje de error -->
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!-- Botón para cerrar alerta -->
                    </div> <!-- Fin alerta de error -->
                            <?php
                        } // Cierra condición de error=1
                        ?> <!-- Fin bloque PHP -->

                        <?php 
            if(isset( $_REQUEST["error"] ) and  $_REQUEST['error'] =='2'){ // Si hay parámetro error=2 (pago exitoso)
                ?> <!-- Cierra PHP para imprimir HTML -->
            <div class="alert alert-success alert-dismissible fade show" role="alert"> <!-- Alerta de éxito -->
            <strong>Excelente</strong> Se realizó el pago exitosamente con la Orden de compra N° <?php echo $_GET['id']?>. <!-- Mensaje de éxito y muestra ID -->
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!-- Botón para cerrar alerta -->
          </div> <!-- Fin alerta de éxito -->
                <?php
            } // Cierra condición de error=2
            ?> <!-- Fin bloque PHP -->
            </div> <!-- Fin cabecera -->
            
            <?php 
            if(sizeof($compras)>=1){ // Si existe al menos un registro en $compras -->
                ?> <!-- Cierra PHP para imprimir HTML -->
            <div class="row"> <!-- Fila principal de verificación -->
                <div class="col-12"> <!-- Columna de ancho completo -->
                    <div class="row"> <!-- Fila interna -->
                        <div class="col-12"> <!-- Columna de ancho completo -->
                        
                         <h4>Datos para el pago</h4> <!-- Título de la tabla de datos de pago -->
                        <!--datos del pago-->
                        <div class="table-responsive"> <!-- Contenedor responsive para la tabla -->
                        <table class="table table-bordered"> <!-- Tabla con bordes -->
                            <thead> <!-- Cabecera de la tabla -->
                                <tr> <!-- Fila de cabecera -->
                                <th>Dato</th> <!-- Columna de encabezado: Dato -->
                                <th>Valor</th> <!-- Columna de encabezado: Valor -->
                                </tr> <!-- Fin fila de cabecera -->
                            </thead> <!-- Fin cabecera de la tabla -->
                            <tbody> <!-- Cuerpo de la tabla -->
                                <tr> <!-- Fila: orden -->
                                    <td>Orden de compra N°</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->id?></td> <!-- Celda valor: ID de la orden -->
                                </tr> <!-- Fin fila orden -->
                                <tr> <!-- Fila: método de pago -->
                                    <td>Método de pago</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->pasarela?></td> <!-- Celda valor: nombre de pasarela -->
                                </tr> <!-- Fin fila método -->
                                <tr> <!-- Fila: nombre -->
                                    <td>Nombre</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $userdata->user_firstname." ".$userdata->user_lastname?></td> <!-- Celda valor: nombre completo del usuario -->
                                </tr> <!-- Fin fila nombre -->
                                <tr> <!-- Fila: email -->
                                    <td>E-Mail</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $userdata->user_email?></td> <!-- Celda valor: email del usuario -->
                                </tr> <!-- Fin fila email -->
                                <tr> <!-- Fila: teléfono -->
                                    <td>Teléfono</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->telefono?></td> <!-- Celda valor: teléfono de la orden -->
                                </tr> <!-- Fin fila teléfono -->
                                <tr> <!-- Fila: dirección -->
                                    <td>Dirección</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->direccion?></td> <!-- Celda valor: dirección de envío -->
                                </tr> <!-- Fin fila dirección -->
                                <tr> <!-- Fila: ciudad -->
                                    <td>Ciudad</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->ciudad?></td> <!-- Celda valor: ciudad -->
                                </tr> <!-- Fin fila ciudad -->
                                <tr> <!-- Fila: observaciones -->
                                    <td>Observaciones</td> <!-- Celda dato: etiqueta -->
                                    <td><?php echo $compras[0]->observaciones?></td> <!-- Celda valor: observaciones -->
                                </tr> <!-- Fin fila observaciones -->
                            </tbody> <!-- Fin cuerpo de la tabla -->
                        </table> <!-- Fin tabla de datos -->
                        </div> <!-- Fin contenedor responsive -->
                        <!--/datos del pago-->
                        </div> <!-- Fin columna -->
                        <hr/> <!-- Separador -->

                        <div class="col-12"> <!-- Columna de ancho completo -->
                        <h4>Productos</h4> <!-- Título de la sección de productos -->
                        <!--productos-->
                        <div class="table-responsive"> <!-- Contenedor responsive para la tabla de productos -->
                            <table class="table table-bordered"> <!-- Tabla de productos -->
                                <thead> <!-- Cabecera de productos -->
                                    <tr> <!-- Fila de cabecera -->
                                        <th>Foto</th> <!-- Encabezado: Foto -->
                                        <th>Producto</th> <!-- Encabezado: Producto -->
                                        <th>Precio</th> <!-- Encabezado: Precio unitario -->
                                        <th>Cantidad</th> <!-- Encabezado: Cantidad -->
                                        <th>Total</th> <!-- Encabezado: Total por línea -->
                                    </tr> <!-- Fin fila cabecera -->
                                </thead> <!-- Fin cabecera -->
                                <tbody> <!-- Cuerpo de la tabla -->
                                <?php 
                                $sum=0; // Inicializa acumulador del total
                                foreach($compras as $compra){ // Recorre cada producto del carrito/compra
                                    $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad; // Suma precio*cantidad al total
                                    ?> <!-- Cierra PHP para imprimir HTML de la fila -->
                                        <tr> <!-- Fila de producto -->
                                        <td style="text-align:center;"> <!-- Celda foto centrada -->
                                <a href="<?php echo get_site_url()."/".$compra->post_name?>" title="<?php echo $compra->post_title ?>"> <!-- Enlace a la ficha del producto -->
                                <img class="img-fluid foto-mis-compras" src="<?php echo  get_site_url().substr(wp_get_attachment_image_src( get_post_thumbnail_id($compra->producto_id), 'post')[0],strlen(get_site_url()), strlen(wp_get_attachment_image_src( get_post_thumbnail_id($compra->producto_id), 'post')[0])); ?>" alt="?php echo $compra->post_title ?>" /> <!-- Miniatura del producto -->
                                </a> <!-- Fin enlace a producto -->
                            </td> <!-- Fin celda foto -->
                                        <td><a href="<?php echo get_site_url()."/".$compra->post_name;?>" class="checkout_title" title="<?php echo $compra->post_title;?>"> <?php echo $compra->post_title;?></a>
                                        </td>  <!-- Celda con nombre y enlace del producto -->
                                        <td>$<?php echo number_format( get_post_meta( $compra->producto_id, 'precio' )[0], 0, '', '.');?></td>  <!-- Celda precio unitario formateado -->
                                        <td><?php echo $compra->cantidad;?></td>  <!-- Celda cantidad -->
                                        <td>$<?php echo number_format( get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad, 0, '', '.')?></td>  <!-- Celda total línea -->
                                    </tr> <!-- Fin fila de producto -->
                                    <?php 
                                } // Fin foreach productos
                                    ?> <!-- Fin bloque PHP -->
                                </tbody> <!-- Fin cuerpo tabla productos -->
                                <tfoot> <!-- Pie de tabla con total -->
                                    <tr> <!-- Fila de total -->
                                        <td></td> <!-- Celda vacía para alineación -->
                                        <td></td> <!-- Celda vacía para alineación -->
                                        <td></td> <!-- Celda vacía para alineación -->
                                        <td><strong>Total:</strong></td> <!-- Celda etiqueta Total -->
                                        <td  ><strong>$<?php echo number_format($sum,0,'','.');?></strong> </td> <!-- Celda valor Total formateado -->
                                    </tr> <!-- Fin fila total -->
                                </tfoot> <!-- Fin pie de tabla -->
                            </table> <!-- Fin tabla de productos -->
                            </div> <!-- Fin contenedor responsive -->
                        <!--/productos-->
                        <hr/> <!-- Separador final de productos -->

                        </div> <!-- Fin columna -->
                        <div class="col-6"> <!-- Columna izquierda (botón volver) -->
                        <a href="<?php echo get_site_url()."/checkout"?>" class="btn btn-warning" title="Volver al checkout"><i class="fas fa-arrow-left"></i> Volver al checkout</a> <!-- Botón para regresar al checkout -->
                        </div> <!-- Fin columna izq -->

                        <div class="col-6"> <!-- Columna derecha (botón pagar según pasarela) -->
                        <?php 
                        switch($compras[0]->tipo_pago){ // Selecciona la pasarela según el tipo elegido
                            case '2': // Caso PayPal
                                $pagar=itc_tienda_paypal_token($compras); // Genera token/link de aprobación PayPal (renombrado a itc_)
                                $wpdb->query("update {$wpdb->prefix}itc_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'"); // Guarda monto y token en el carro con prefijo itc_
                                ?> <!-- Cierra PHP para imprimir HTML del botón -->
                                <a href="<?php echo $pagar['url']?>" class="btn btn-warning" title="Pagar con <?php echo $compras[0]->pasarela?>"> <!-- Botón que redirige a PayPal -->
                                <i class="fab fa-paypal"></i> Pagar con <?php echo $compras[0]->pasarela?> <!-- Texto del botón -->
                                </a> <!-- Fin botón PayPal -->
                                <?php

                            break; // Fin caso PayPal

                            /*
                            case '2': // Caso Mercado Pago
                                $pagar=itc_tienda_mercado_pago_token($compras); // Genera preferencia/URL de Mercado Pago (renombrado a itc_)
                                $wpdb->query("update {$wpdb->prefix}itc_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'"); // Guarda monto y token en el carro con prefijo itc_
                                ?> <!-- Cierra PHP para imprimir HTML del botón -->
                                <a href="<?php echo $pagar['url']?>" class="btn btn-warning" title="Pagar con <?php echo $compras[0]->pasarela?>"> <!-- Botón que redirige a Mercado Pago -->
                                <i class="far fa-handshake"></i> Pagar con <?php echo $compras[0]->pasarela?> <!-- Texto del botón -->
                                </a> <!-- Fin botón Mercado Pago -->
                                <?php
                            break; // Fin caso Mercado Pago

                            case '3': // Caso Stripe (Checkout Session)
                                $pagar=itc_tienda_stripe_obtener_token($compras); // Crea la sesión/URL de Stripe (renombrado a itc_)
                                $wpdb->query("update {$wpdb->prefix}itc_tienda_carro
                                set 
                                monto='".$sum."',
                                token='".$pagar['token']."' 
                                where id='".$compras[0]->id."'"); // Guarda monto y token en el carro con prefijo itc_
                                ?> <!-- Cierra PHP para imprimir HTML del botón -->
                                <a href="<?php echo $pagar['url']?>" class="btn btn-info" title="Pagar con <?php echo $compras[0]->pasarela?>"> <!-- Botón que redirige a Stripe Checkout -->
                                <i class="fab fa-stripe"></i> Pagar con <?php echo $compras[0]->pasarela?> <!-- Texto del botón -->
                                </a> <!-- Fin botón Stripe -->
                                <?php
                            break; // Fin caso Stripe
                            */
                        } // Fin switch tipo de pago
                        ?> <!-- Fin bloque PHP de botones de pago -->
                        </div> <!-- Fin columna derecha -->
                    </div> <!-- Fin fila interna -->
                </div> <!-- Fin columna 12 -->
            </div> <!-- Fin fila principal -->
                <?php
            } // Fin if sizeof($compras)>=1
            ?> <!-- Fin bloque PHP -->
            
        </main> <!-- Fin del contenido principal -->
    </div> <!-- Fin del contenedor -->
        <?php
    } // Fin de la función del shortcode
} // Fin del if !function_exists
