<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';
if(!function_exists('tamila_tienda_paypal_consume_api')){
    function tamila_tienda_paypal_consume_api($url, $data, $jwt, $orden)
    {
       
       $headers=array();
       $headers[] = 'Content-Type: application/json';
       $headers[] = 'Authorization: Bearer '.$jwt;
       $headers[] = 'PayPal-Request-Id: order_'.$orden;
       //print_r($headers);exit;
       //inicializamos la conexión CURL
       $curl = curl_init($url); 
       curl_setopt($curl, CURLOPT_POST, 1);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
       $curl_response = curl_exec($curl);
       $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT);
    
       curl_close($curl);
       return json_decode($curl_response);
    }
}
if(!function_exists('tamila_tienda_paypal_obtener_jwt')){
    function tamila_tienda_paypal_obtener_jwt(){
        global $wpdb;
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =2;", ARRAY_A);
        $headers=array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $curl = curl_init($datos[0]['url']."/v1/oauth2/token"); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $datos[0]['cliente_id'].":".$datos[0]['cliente_secret']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT); 
        curl_close($curl);
        return json_decode($curl_response)->access_token;

    }
}
if(!function_exists('tamila_tienda_paypal_token')){
    function tamila_tienda_paypal_token($compras){
        global $wpdb;
        $userdata=wp_get_current_user();
        $jwt=tamila_tienda_paypal_obtener_jwt();
        $sum=0;
          foreach($compras as $compra){
            
            $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
        }
        $dolar=tamila_tienda_obtener_valor_dolar();
        $dolares=$sum/$dolar;
        //obtenemos la metadata de paypal
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =2;", ARRAY_A);
        //hacemos la petición a paypal
        $json=array(
            'intent' => 'CAPTURE',
            'purchase_units' => [
                0 => [
                    "reference_id"=> "order_".$compras[0]->id ,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' =>number_format($dolares, 0,'', ''),
                    ]
                ]
            ],
            'payment_source' => [
                'paypal'=>[
                    'experience_context'=>[
                        "payment_method_preference"=>"IMMEDIATE_PAYMENT_REQUIRED",
                        "payment_method_selected"=> "PAYPAL",
                        "brand_name"=> "Tamila",
                        "locale"=> "es-ES",
                        "landing_page"=> "LOGIN",
                        "shipping_preference"=> "NO_SHIPPING",
                        "user_action"=> "PAY_NOW",
                        "return_url"=> get_site_url()."/verificacion",
                        "cancel_url"=> get_site_url()."/verificacion"
                    ]
                    
                ]
                
            ]
        );
        $response=tamila_tienda_paypal_consume_api($datos[0]['url']."/v2/checkout/orders", json_encode($json), $jwt, $compras[0]->id);
        return ['url'=>$response->links[1]->href,'token'=>$response->id];
    }
}
if(!function_exists('tamila_tienda_paypal_captura')){
    function tamila_tienda_paypal_captura($token){
        global $wpdb;
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =2;", ARRAY_A);
        $captura=tamila_tienda_paypal_consume_api($datos[0]['url']."/v2/checkout/orders/".$token."/capture", '{}', tamila_tienda_paypal_obtener_jwt(), '1');
        return $captura->status;
    }
}