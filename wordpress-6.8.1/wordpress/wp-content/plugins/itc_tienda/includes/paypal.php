<?php 
// Evita que el archivo se ejecute directamente sin pasar por WordPress
if(!defined('ABSPATH')) die();

// Incluye el archivo de utilidades del plugin
require_once plugin_dir_path( __FILE__ ) . 'utilidades.php';

// Verifica si la función aún no existe, para no redeclararla
if(!function_exists('itc_tienda_paypal_consume_api')){
    
    // Función que se encarga de consumir la API de PayPal
    function itc_tienda_paypal_consume_api($url, $data, $jwt, $orden)
    {
       // Definimos los headers necesarios para la petición HTTP
       $headers=array();
       $headers[] = 'Content-Type: application/json'; // El contenido será en JSON
       $headers[] = 'Authorization: Bearer '.$jwt;   // Token de autorización (JWT)
       $headers[] = 'PayPal-Request-Id: order_'.$orden; // ID único para la orden

       // Inicializamos la conexión CURL hacia la URL de PayPal
       $curl = curl_init($url); 
       curl_setopt($curl, CURLOPT_POST, 1); // Es una petición POST
       curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Datos que se envían a PayPal
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retorna la respuesta como string
       curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Agrega los headers configurados

       // Ejecuta la petición CURL
       $curl_response = curl_exec($curl);

       // Obtiene información de la respuesta (por ejemplo cabeceras)
       $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT);

       // Cierra la conexión CURL
       curl_close($curl);

       // Retorna la respuesta decodificada en formato objeto PHP
       return json_decode($curl_response);
    }
}

// Verifica si no existe la función para obtener el JWT de PayPal
if(!function_exists('itc_tienda_paypal_obtener_jwt')){
    function itc_tienda_paypal_obtener_jwt(){
        global $wpdb; // Acceso a la base de datos de WordPress

        // Obtenemos las credenciales de PayPal desde la tabla personalizada
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas where id =2;", ARRAY_A);

        // Armamos los headers para la petición
        $headers=array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        // Iniciamos CURL para pedir un token a PayPal
        $curl = curl_init($datos[0]['url']."/v1/oauth2/token"); 
        curl_setopt($curl, CURLOPT_POST, 1); // Petición POST
        curl_setopt($curl, CURLOPT_USERPWD, $datos[0]['cliente_id'].":".$datos[0]['cliente_secret']); // Cliente y secreto
        curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // Tipo de grant para credenciales
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retorna como string
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Headers configurados

        // Ejecutamos CURL y obtenemos la respuesta
        $curl_response = curl_exec($curl);

        // Obtenemos información de la petición
        $http_code = curl_getinfo($curl, CURLINFO_HEADER_OUT); 

        // Cerramos la conexión CURL
        curl_close($curl);

        // Retornamos el token de acceso (JWT) obtenido de PayPal
        return json_decode($curl_response)->access_token;
    }
}

// Verifica si no existe la función para generar un token de orden
if(!function_exists('itc_tienda_paypal_token')){
    function itc_tienda_paypal_token($compras){
        global $wpdb; // Acceso a la base de datos
        $userdata=wp_get_current_user(); // Obtenemos datos del usuario actual
        $jwt=itc_tienda_paypal_obtener_jwt(); // Obtenemos token JWT de PayPal

        // Sumamos el valor total de las compras
        $sum=0;
        foreach($compras as $compra){
            $sum=$sum+get_post_meta( $compra->producto_id, 'precio' )[0]*$compra->cantidad;
        }

        // Obtenemos el valor del dólar desde función auxiliar
        $dolar=itc_tienda_obtener_valor_dolar();

        // Convertimos el monto total de la compra a dólares
        $dolares=$sum/$dolar;

        // Obtenemos la configuración de la pasarela PayPal desde la base de datos
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas where id =2;", ARRAY_A);

        // Armamos el JSON que se enviará a PayPal con los detalles de la orden
        $json=array(
            'intent' => 'CAPTURE', // El pago se captura directamente
            'purchase_units' => [
                0 => [
                    "reference_id"=> "order_".$compras[0]->id , // Referencia interna
                    'amount' => [
                        'currency_code' => 'USD', // Moneda en dólares
                        'value' =>number_format($dolares, 0,'', ''), // Valor total
                    ]
                ]
            ],
            'payment_source' => [
                'paypal'=>[
                    'experience_context'=>[
                        "payment_method_preference"=>"IMMEDIATE_PAYMENT_REQUIRED", // Pago inmediato
                        "payment_method_selected"=> "PAYPAL", // Método seleccionado
                        "brand_name"=> "ITC", // Nombre que verá el cliente en PayPal
                        "locale"=> "es-ES", // Idioma
                        "landing_page"=> "LOGIN", // Página de login de PayPal
                        "shipping_preference"=> "NO_SHIPPING", // Sin dirección de envío
                        "user_action"=> "PAY_NOW", // Botón directo de pagar
                        "return_url"=> get_site_url()."/verificacion", // URL al volver
                        "cancel_url"=> get_site_url()."/verificacion" // URL si cancela
                    ]
                ]
            ]
        );

        // Ejecutamos la creación de la orden en PayPal
        $response=itc_tienda_paypal_consume_api($datos[0]['url']."/v2/checkout/orders", json_encode($json), $jwt, $compras[0]->id);

        // Retornamos la URL de PayPal y el token de la orden
        return ['url'=>$response->links[1]->href,'token'=>$response->id];
    }
}

// Verifica si no existe la función para capturar un pago de PayPal
if(!function_exists('itc_tienda_paypal_captura')){
    function itc_tienda_paypal_captura($token){
        global $wpdb; // Acceso a la base de datos

        // Obtenemos la configuración de la pasarela PayPal
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas where id =2;", ARRAY_A);

        // Llamamos a PayPal para capturar el pago de la orden
        $captura=itc_tienda_paypal_consume_api($datos[0]['url']."/v2/checkout/orders/".$token."/capture", '{}', itc_tienda_paypal_obtener_jwt(), '1');

        // Retornamos el estado de la captura (ej: COMPLETED)
        return $captura->status;
    }
}
