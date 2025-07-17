<?php
if(!defined('ABSPATH')) die();
//ésto para que no haga nada si se presionó la función por error
if(!defined('WP_UNINSTALL_PLUGIN')){
    die();
}
if(!function_exists('itc_contact_eliminar')){
    function itc_eliminar(){
        global $wpdb;
        $wpdb->query("drop table {$wpdb->prefix}itc_contact_respuestas");
        $wpdb->query("drop table {$wpdb->prefix}itc_contact");
    }
}
itc_contact_eliminar();