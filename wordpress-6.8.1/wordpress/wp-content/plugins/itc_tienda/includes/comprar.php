<?php
if (!defined("ABSPATH")) {
    die();
}
add_action('wp_ajax_itc_tienda_comprar_ajax', function(){
    global $wpdb; 
    $userdata=wp_get_current_user();
    if(sanitize_text_field( $_POST['accion'] )=='1'){
        $cuantos=$wpdb->get_results("select id from {$wpdb->prefix}itc_tienda_carro where usuario_id='".$userdata->ID."' and estado_id in (1,6);"); // verificamos si el usuario tiene un carro activo o no
        if(sizeof($cuantos)==0){
            $wpdb->query(
                $wpdb->prepare("insert into {$wpdb->prefix}itc_tienda_carro values(null, '".$userdata->ID."', '1', '', '', '','', now(), now(),1, '', 0);") //prepare sirve para evitar inyecciones SQL // seteamos metodos de pago y envío vacíos por si el usuario no los selecciona
            );
            //ésto $wpdb->insert_id, nos permite obtener el último id creado
            $wpdb->query("insert into {$wpdb->prefix}itc_tienda_carro_detalle values(null, '".$wpdb->insert_id."', '".sanitize_text_field($_POST['id'])."', '".sanitize_text_field($_POST['product_quanity'])."');");
        }else{
            $wpdb->query("insert into {$wpdb->prefix}itc_tienda_carro_detalle values(null, '".$cuantos[0]->id."', '".sanitize_text_field($_POST['id'])."', '".sanitize_text_field($_POST['product_quanity'])."');");
        }
       
    }else{
        $carro=$wpdb->query("delete from {$wpdb->prefix}itc_tienda_carro_detalle where id='".sanitize_text_field( $_POST['carro_detalle_id'] )."';");
    }
});