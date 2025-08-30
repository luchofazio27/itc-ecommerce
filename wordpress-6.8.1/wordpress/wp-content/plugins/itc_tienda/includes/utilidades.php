<?php
if (!defined('ABSPATH')) die();
//JWT composer require firebase/php-jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
//composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if (!function_exists('itc_tienda_envia_correo')) {
    function itc_tienda_envia_correo($correo, $asunto, $mensaje)
    {
        global $wpdb;
        $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}itc_tienda_variables_globales where id in(1,2,3,4);", ARRAY_A);

        require_once __DIR__ . '/vendor/autoload.php';
        $mail = new PHPMailer(true);


        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = $datos[0]['valor'];
            $mail->SMTPAuth   = true;
            $mail->Username = $datos[1]['valor'];
            $mail->Password = $datos[2]['valor'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $datos[3]['valor'];
            $mail->CharSet = 'UTF-8';
            //$mail->setFrom($datos[1]['valor'], $asunto);
            $mail->setFrom('from@example.com', 'ITC Tienda');
            $mail->addAddress($correo, get_bloginfo('name'));

            $mail->isHTML(true);
            $mail->Subject = 'Asunto de el mail';
            $mail->Body = $mensaje;

            $mail->send();
            return true;
        } catch (Exception $e) {
        return false;
}

    }
}

if (!function_exists('itc_tienda_generate_jwt')) {
    function itc_tienda_generate_jwt($payload)
    {
        global $wpdb;
        $datos = $wpdb->get_results("select nombre, valor from {$wpdb->prefix}itc_tienda_variables_globales where id in(5);", ARRAY_A);
        require 'vendor/autoload.php';
        return JWT::encode($payload, $datos[0]['valor'], 'HS512');
    }
}
if (!function_exists('itc_tienda_obtener_valor_dolar')) {
    function itc_tienda_obtener_valor_dolar()
    {
        $response = wp_remote_get('https://api.dolarapi.com/v1/dolares/oficial');
        
        if (is_wp_error($response)) {
            return 1; // fallback si falla
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Devuelve el valor de venta (precio al que se compra USD en Argentina)
        return isset($data['venta']) ? floatval($data['venta']) : 1;
    }
}
