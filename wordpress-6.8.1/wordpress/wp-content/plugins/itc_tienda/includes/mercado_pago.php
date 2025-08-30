<?php
if (!defined('ABSPATH')) die();

// Cargamos Composer
require_once __DIR__ . '/vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

if (!function_exists('itc_tienda_mercado_pago_token')) {
    function itc_tienda_mercado_pago_token($compras)
    {
        global $wpdb;

        if (empty($compras)) return false; // No hay compras

        // Obtenemos credenciales de la DB
        $datos = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}itc_tienda_carro_pasarelas WHERE id = 3;", ARRAY_A);
        if (empty($datos)) return false;

        $accessToken = $datos[0]['cliente_secret'];
        MercadoPagoConfig::setAccessToken($accessToken);

        // Creamos cliente de preferencias
        $client = new PreferenceClient();

        // Preparamos los items
        $items = [];
        foreach ($compras as $compra) {
            $precio = floatval(get_post_meta($compra->producto_id, 'precio', true));
            $cantidad = intval($compra->cantidad) ?: 1;

            $items[] = [
                "title" => !empty($compra->post_title) ? $compra->post_title : "Producto",
                "quantity" => $cantidad,
                "unit_price" => $precio ?: 1
            ];
        }

        try {
            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success"=>"https://wwww.tienda.tamila.cl/verificacion",
                    "failure"=>"https://wwww.tienda.tamila.cl/verificacion",
                    "pending"=>"https://wwww.tienda.tamila.cl/verificacion"
                ],
                "auto_return" => "approved"
            ]);

            return [
                "token" => $preference->id ?? null,
                "url" => $preference->init_point ?? null
            ];

        } catch (MPApiException $e) {
            // Devuelve detalles del error de Mercado Pago para depuración
            echo "<pre>";
            print_r($e->getApiResponse());
            echo "</pre>";
            return false;
        } catch (\Exception $e) {
            echo "Error inesperado: " . $e->getMessage();
            return false;
        }
    }
}
