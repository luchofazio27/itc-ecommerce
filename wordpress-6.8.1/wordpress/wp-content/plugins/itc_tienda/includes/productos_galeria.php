<?php 
if(!defined('ABSPATH')) die();
if(!function_exists('itc_tienda_productos_galeria_init')){
    function itc_tienda_productos_galeria_init(){
        global $wpdb;
        $sql ="create table if not exists 
            {$wpdb->prefix}itc_tienda_productos_galeria
            (
            id int not null auto_increment,
            nombre varchar(500) not null,
            post_id int,
            foto_id int,
            primary key (id)
            ); 
            ";
        $wpdb->query($sql);
        $index= "alter table {$wpdb->prefix}itc_tienda_productos_galeria add index(`nombre`);";
        $wpdb->query($index);
        $index2= "alter table {$wpdb->prefix}itc_tienda_productos_galeria add index(`post_id`);";
        $wpdb->query($index2);
        $index3= "alter table {$wpdb->prefix}itc_tienda_productos_galeria add index(`foto_id`);";
        $wpdb->query($index3);
    }
}
if(!function_exists('itc_tienda_productos_galeria_desactivar')){
    function itc_tienda_productos_galeria_desactivar(){
        global $wpdb;
        $sql="drop table {$wpdb->prefix}itc_tienda_productos_galeria";
        $wpdb->query($sql);
    }
}
###cargamos el menú
if(!function_exists('itc_tienda_productos_crear_menu')){
    function itc_tienda_productos_crear_menu(){
        add_menu_page( 
            "Productos Galería", 
            "Producto Galería", 
            "manage_options",  
            plugin_dir_path( __FILE__ )."admin/listar.php", 
            null, 
            'dashicons-embed-photo', 
            131 );
        add_submenu_page( 
                null, 
                "Editar", //Título del menú
                null, //título de la página
                "manage_options", 
                plugin_dir_path( __FILE__ )."admin/editar.php", 
                null  );
    }
    add_action('admin_menu', 'itc_tienda_productos_crear_menu');
}