<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';
//https://dashboard.stripe.com/test/apikeys
//https://stripe.com/docs/testing?locale=es-419 
// tarjeta 4242 4242 4242 4242 12/34 123 cualquier nombre
//https://stripe.com/docs/api/products/create
//https://stripe.com/docs/api/prices/create
//https://stripe.com/docs/api/checkout/sessions/create
if(!function_exists('tamila_tienda_stripe_consume_api')){
    function tamila_tienda_stripe_consume_api($endpoint, $data){
       global $wpdb;
       $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =4;", ARRAY_A);
       $headers=array();
       $headers[] = 'Content-Type: application/x-www-form-urlencoded';
       $headers[] = 'Authorization: Bearer '.$datos[0]['cliente_secret'];
       //inicializamos la conexión CURL
       $curl = curl_init($datos[0]['url'].$endpoint); 
       curl_setopt($curl, CURLOPT_POST, 1);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
       $curl_response = curl_exec($curl);
       $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT);
       //por favor cerrar la conexión
       curl_close($curl); 
       return json_decode($curl_response);
    }
}
if(!function_exists('tamila_tienda_stripe_obtener_token')){
    function tamila_tienda_stripe_obtener_token($compras){
       global $wpdb;
       $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =4;", ARRAY_A);
       $sum=0;
       foreach($compras as $compra){
         $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
        }
        $dolar=tamila_tienda_obtener_valor_dolar();
        $dolares=$sum/$dolar;   
        $producto=tamila_tienda_stripe_consume_api("/v1/products", "name=".$compras[0]->post_title);
        $precio=tamila_tienda_stripe_consume_api("/v1/prices", "unit_amount=".number_format($dolares, 0, '', '')."&currency=usd&product=".$producto->id);
        $checkout=tamila_tienda_stripe_consume_api("/v1/checkout/sessions", "success_url=".get_site_url()."/verificacion?stripe=".$compras[0]->id."&currency=usd&line_items[0][price]=".$precio->id."&line_items[0][quantity]=1&mode=payment");
        return ['url'=>$checkout->url,'token'=>$checkout->id];
    }
}