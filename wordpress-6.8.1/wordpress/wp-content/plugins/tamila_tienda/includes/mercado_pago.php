<?php 
if(!defined('ABSPATH')) die();
use MercadoPago; 
//https://github.com/mercadopago/sdk-php
//composer require "mercadopago/dx-php:2.5.5"
if(!function_exists('tamila_tienda_mercado_pago_token')){
    function tamila_tienda_mercado_pago_token($compras){
        global $wpdb;
        $userdata=wp_get_current_user();
        $sum=0;
          foreach($compras as $compra){
            
            $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
        }
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =3;", ARRAY_A);
        require_once 'vendor/autoload.php';
        $token=$datos[0]['cliente_secret'];
         
        MercadoPago\SDK::setAccessToken($token); 
        $preference = new MercadoPago\Preference();
        $preference->back_urls=
            array(
                "success"=>"https://wwww.tienda.tamila.cl/verificacion",
                "failure"=>"https://wwww.tienda.tamila.cl/verificacion",
                "pending"=>"https://wwww.tienda.tamila.cl/verificacion"
            );
        $monto=10;
         
        $preference->auto_return="approved";
        $item=new MercadoPago\Item();
        $item->title=$compras[0]->post_title;
        $item->quantity=2;
        $item->unit_price=$monto;

        $preference->items=array($item);
        $preference->save();   
        return array('token'=>$preference->id, 'url'=>$preference->init_point);
    }
}