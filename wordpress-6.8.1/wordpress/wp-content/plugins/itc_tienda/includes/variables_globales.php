<?php 
if(!defined('ABSPATH')) die();
if(!function_exists('itc_tienda_variables_globales_post')){
    function itc_tienda_variables_globales_post() {
        if (isset($_POST['nonce']) && (($_POST['action'] ?? '') == 'variables-globales-edit')) {
            global $wpdb;
            $wpdb->query("update {$wpdb->prefix}itc_tienda_variables_globales 
                    set  
                    nombre='".sanitize_text_field($_POST['nombre'])."',
                    valor='".sanitize_text_field($_POST['valor'])."' 
                    where 
                    id='".sanitize_text_field($_POST['variables_globales_id'])."';");
            wp_safe_redirect( $_POST['return'] ); exit;
        }
    }
    }
    add_action( 'after_setup_theme', 'itc_tienda_variables_globales_post' );

//ajax
if(!function_exists('itc_tienda_carro_variables_globales_ajax')){
    function itc_tienda_carro_variables_globales_ajax(){
        global $wpdb;
        
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_variables_globales where id='".sanitize_text_field($_POST['id'])."';", ARRAY_A); 
        ?>
        <div class="row">
            <form action="" method="POST" name="itc_tienda_form_variables_globales">
               
                <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="<?php echo $datos[0]['nombre'];?>" />
                </div>
                <div class="mb-3">
                <label for="valor" class="form-label">Valor:</label>
                <textarea name="valor" id="valor" class="form-control" placeholder="Valor"><?php echo $datos[0]['valor'];?></textarea>
                </div>
                 
                <hr/>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
                <input type="hidden" name="action" value="variables-globales-edit" />
                <input type="hidden" name="return" value="" />
                <input type="hidden" name="variables_globales_id" value="<?php echo sanitize_text_field($_POST['id'])?>" />
                <a href="javascript:void(0);" class="btn btn-primary" title="Editar" onclick="edit_variables_globales();"><i class="fas fa-edit"></i> Editar</a>
            </form>
        </div>
        <?php
        die();
    }
    add_action('wp_ajax_itc_tienda_variables_globales_ajax','itc_tienda_carro_variables_globales_ajax');
}