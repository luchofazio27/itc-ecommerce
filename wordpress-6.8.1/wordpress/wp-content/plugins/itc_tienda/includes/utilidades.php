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
        $datos = $wpdb->get_results(
            "SELECT nombre, valor FROM {$wpdb->prefix}itc_tienda_variables_globales WHERE id IN (1,2,3,4);",
            ARRAY_A
        );

        require_once __DIR__ . '/vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            // No mostrar debug en frontend
            $mail->SMTPDebug = SMTP::DEBUG_OFF;

            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host       = $datos[0]['valor']; // smtp_server
            $mail->SMTPAuth   = true;
            $mail->Username   = $datos[1]['valor']; // smtp_user
            $mail->Password   = $datos[2]['valor']; // smtp_password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $datos[3]['valor']; // smtp_port
            $mail->CharSet    = 'UTF-8';

            // Opciones SSL para localhost / certificados autofirmados
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // FROM fijo (cuenta SMTP)
            $mail->setFrom($datos[1]['valor'], 'ITC Tienda');

            // Destinatario dinámico (correo del formulario)
            $mail->addAddress($correo);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Registrar error en log de PHP para no mostrar en frontend
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
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
