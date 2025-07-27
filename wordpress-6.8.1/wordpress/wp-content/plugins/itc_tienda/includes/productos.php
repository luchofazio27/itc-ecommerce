<?php
if(!defined('ABSPATH')) die();
if(!function_exists('itc_tienda_post_type_init')){
    function itc_tienda_post_type_init(){
        $labels=[
            'name'=>'Productos',
            'singular_name'=>'Producto',
            'add_new'=>'Agregar Nuevo',
            'add_new_item'=>'Agregar Nuevo producto',
            'edit_item'=>'Editar producto',
            'view_item'=>'Ver productos',
            'featured_image'        =>'Imagen de portada',
            'set_featured_image'    => 'Guardar Imagen de portada',
            'remove_featured_image' => 'Eliminar Imagen de portada',
            'use_featured_image'    =>  'Utilizar como Imagen de portada',

        ];
        $args=[
            'labels'=>$labels,
            'public'=>true,//indica que sea público para ser mostrado,
            'has_archive'=>true,//indica si habrá una página para administrar nuestro post_type
            'menu_position'         => 130,
            'menu_icon'             => 'dashicons-menu-alt',
            'capability_type'       => 'post',
            'supports'              => array(  'title', 'editor', 'thumbnail', 'custom-fields'),
            'show_ui'               => true,//para que no aparezca submenú
            'show_in_menu'          => true,//para que aparezca en el menú
            //'show_in_menu'=>'itc_post_type'
            'show_in_admin_bar'     => true,//para que aparezca o no en el menú añadir de l navbar
		    'show_in_nav_menus'     => true,//para que aparezca o no en el menú añadir de l navbar
            'taxonomies'          => array( 'category' ),
            'rewrite'=>['slug'=> 'tienda'],
            'show_in_rest'=>true
        ];
        register_post_type('itc_productos', $args);
        flush_rewrite_rules(); //refresca las reglas de reescritura
    }
    add_action('init', 'itc_tienda_post_type_init');
}