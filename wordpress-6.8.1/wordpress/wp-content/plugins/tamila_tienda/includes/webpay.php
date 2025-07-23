<?php 
if(!defined('ABSPATH')) die();
if(!function_exists('tamila_tienda_webpay_consume_api')){
    function tamila_tienda_webpay_consume_api($metodo, $url, $data){
        global $wpdb;
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =1;", ARRAY_A);
        $headers=array();
        $headers[]='Content-Type: application/json';
        $headers[]='Tbk-Api-Key-Id:'.$datos[0]['cliente_id'];
        $headers[] = 'Tbk-Api-Key-Secret:'.$datos[0]['cliente_secret'];
        //inicializar una petición CURL
        $curl=curl_init($url);
        switch ($metodo)
       {
          case "POST":
            
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
          case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                                            
           break;
         }
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
         $curl_response = curl_exec($curl);
         $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT);
         //por favor cerrar la conexión
         curl_close($curl);
         return json_decode($curl_response);
    }
}
if(!function_exists('tamila_tienda_webpay_token')){
    function tamila_tienda_webpay_token($compras){
        global $wpdb;
        $userdata=wp_get_current_user();
        $sum=0;
        foreach($compras as $compra){
            
            $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
        }
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =1;", ARRAY_A);
        $json=array(
            'buy_order' => 'orden_'.$compras[0]->id,
            'session_id' =>time() ,
            'amount' => $sum,
            'return_url'=>get_site_url()."/verificacion");
        $response=tamila_tienda_webpay_consume_api("POST", $datos[0]['url'], json_encode($json));
        return ['url'=>$response->url, 'token'=>$response->token];
    }
}
if(!function_exists('tamila_tienda_webpay_verificar')){
    function tamila_tienda_webpay_verificar($token)
    {
        global $wpdb;
        $userdata=wp_get_current_user();
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id =1;", ARRAY_A);
        $endpoint=$datos[0]['url']."/".$token;
        $consume=tamila_tienda_webpay_consume_api('PUT', $endpoint,  "{}");
       
        if($consume->status=='FAILED')
        {
            return ['estado'=>false, 'consume'=> $consume];
        }
        if($consume->status=='AUTHORIZED')
        {
            return ['estado'=>true, 'consume'=> $consume];
        }
         

    }
}