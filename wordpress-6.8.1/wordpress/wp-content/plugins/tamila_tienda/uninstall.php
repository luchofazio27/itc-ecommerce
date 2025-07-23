<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'includes/init.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/slide.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/productos_galeria.php';
tamila_tienda_init_eliminar();
tamila_tienda_slide_desactivar();
tamila_tienda_productos_galeria_desactivar();