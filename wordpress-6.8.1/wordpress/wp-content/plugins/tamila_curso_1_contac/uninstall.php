<?php
if(!defined('ABSPATH')) die();
//ésto para que no haga nada si se presionó la función por error
if(!defined('WP_UNINSTALL_PLUGIN')){
    die();
}
if(!function_exists('tamila_curso_1_contact_eliminar')){
    function tamila_curso_1_contact_eliminar(){
        global $wpdb;
        $wpdb->query("drop table {$wpdb->prefix}tamila_curso_1_contact_respuestas");
        $wpdb->query("drop table {$wpdb->prefix}tamila_curso_1_contact");
    }
}
tamila_curso_1_contact_eliminar();