<?php 
if(!defined('ABSPATH')) die();
//crear páginas
if(!function_exists('tamila_tienda_crear_paginas')){
    function tamila_tienda_crear_paginas(){
        $page1=get_page_by_path('activated', OBJECT);
        if(!isset($page1)){
            $activated=[
                'post_type'=>'page',
                'post_title'=>'Activated',//activated
                'post_content'  => '<div class="container py-5">
                <div class="row py-5">[tamila_tienda_activated id=1]</div>
                </div>',
                'post_status'   => 'publish',
                'post_author'   => 1
                ];
            wp_insert_post( $activated );
        }
        $page2=get_page_by_path( 'restablecer' , OBJECT ); 
        if(!isset($page2)){
          $restablecer = array(
            'post_type'     => 'page',
            'post_title'    => 'Restablecer',
            'post_content'  => '<div class="container py-5">
            <div class="row py-5">[tamila_tienda_restablecer id=1]</div>
            </div>',
            'post_status'   => 'publish',
            'post_author'   => 1
          );
          wp_insert_post( $restablecer );
        }
        $page3=get_page_by_path( 'reset' , OBJECT ); 
        if(!isset($page3)){
          $reset = array(
            'post_type'     => 'page',
            'post_title'    => 'Reset',
            'post_content'  => '<div class="container py-5">
            <div class="row py-5">[tamila_tienda_reset id=1]</div>
            </div>',
            'post_status'   => 'publish',
            'post_author'   => 1
          );
        wp_insert_post( $reset );
        }
        $page4=get_page_by_path( 'login' , OBJECT );  
        if(!isset($page4)){
          $login = array(
            'post_type'     => 'page',
            'post_title'    => 'Login',
            'post_content'  => '<div class="container py-5">
            <div class="row py-5">[tamila_tienda_login id=1]</div>
            </div>',
            'post_status'   => 'publish',
            'post_author'   => 1
          );
          wp_insert_post( $login );
          }
          $page5=get_page_by_path( 'registro' , OBJECT );  
          if(!isset($page5)){
            $registro = array(
              'post_type'     => 'page',
              'post_title'    => 'Registro',
              'post_content'  => '<div class="container py-5">
              <div class="row py-5">[tamila_tienda_registro id=1]</div>
              </div>',
              'post_status'   => 'publish',
              'post_author'   => 1
            );
            wp_insert_post( $registro );
            }
          $page6=get_page_by_path( 'perfil' , OBJECT );  
            if(!isset($page6)){
              $perfil = array(
                'post_type'     => 'page',
                'post_title'    => 'Perfil',
                'post_content'  => '<div class="container py-5">
                <div class="row py-5">[tamila_tienda_perfil id=1]</div>
                </div>',
                'post_status'   => 'publish',
                'post_author'   => 1
              );
              wp_insert_post( $perfil );
              }
          $page7=get_page_by_path( 'checkout' , OBJECT );  
              if(!isset($page7)){
                $checkout = array(
                  'post_type'     => 'page',
                  'post_title'    => 'Checkout',
                  'post_content'  => '<div class="container py-5">
                  <div class="row py-5">[tamila_tienda_checkout id=1]</div>
                  </div>',
                  'post_status'   => 'publish',
                  'post_author'   => 1
                );
                wp_insert_post( $checkout );
                }  
        $page8=get_page_by_path( 'verificacion' , OBJECT );  
                if(!isset($page8)){
                  $verificacion = array(
                    'post_type'     => 'page',
                    'post_title'    => 'Verificación',
                    'post_content'  => '<div class="container py-5">
                    <div class="row py-5">[tamila_tienda_verificacion id=1]</div>
                    </div>',
                    'post_status'   => 'publish',
                    'post_author'   => 1
                  );
                  wp_insert_post( $verificacion );
                  }
          $page9=get_page_by_path( 'tienda' , OBJECT );  
                  if(!isset($page9)){
                    $tienda = array(
                      'post_type'     => 'page',
                      'post_title'    => 'Tienda',
                      'post_content'  => '',
                      'post_status'   => 'publish',
                      'post_author'   => 1
                    );
                    wp_insert_post( $tienda );
                    }
          $page10=get_page_by_path( 'orden-de-venta' , OBJECT );  
                    if(!isset($page10)){
                      $orden_de_venta = array(
                        'post_type'     => 'page',
                        'post_title'    => 'Orden de venta',
                        'post_content'  => '',
                        'post_status'   => 'publish',
                        'post_author'   => 1
                      );
                      wp_insert_post( $orden_de_venta );
                      }
    }
}
if(!function_exists('tamila_tienda_eliminar_paginas')){
    function tamila_tienda_eliminar_paginas(){
        $activated = get_page_by_path( 'activated' );
        wp_delete_post($activated->ID);
        $reset = get_page_by_path( 'reset' );
        wp_delete_post($reset->ID);
        $restablecer = get_page_by_path( 'restablecer' );
        wp_delete_post($restablecer->ID);
        $verificacion = get_page_by_path( 'verificacion' );
        wp_delete_post($verificacion->ID);
        $perfil = get_page_by_path( 'perfil' );
        wp_delete_post($perfil->ID);
        $login = get_page_by_path( 'login' );
        wp_delete_post($login->ID);
        $registro = get_page_by_path( 'registro' );
        wp_delete_post($registro->ID);
        $checkout = get_page_by_path( 'checkout' );
        wp_delete_post($checkout->ID);
        $tienda = get_page_by_path( 'tienda' );
        wp_delete_post($tienda->ID);
        $orden_de_venta = get_page_by_path( 'orden-de-venta' );
        wp_delete_post($orden_de_venta->ID);
    }
}