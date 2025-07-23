<?php 
if(!defined('ABSPATH')) die();

add_action('after_setup_theme', function(){
    if(isset($_POST['nonce'])  and $_POST['action']=='pasarela-edit'){
        global $wpdb;
            $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro_pasarelas 
                    set 
                    estado_id='".sanitize_text_field($_POST['estado_id'])."',
                    url='".sanitize_text_field($_POST['url'])."',
                    cliente_id='".sanitize_text_field($_POST['cliente_id'])."',
                    cliente_secret='".sanitize_text_field($_POST['cliente_secret'])."'
                    where 
                    id='".sanitize_text_field($_POST['pasarela_id'])."';");
        wp_safe_redirect($_POST['return']);exit;
    }
});

 //ajax
 if(!function_exists('tamila_tienda_carro_pasarelas_ajax')){
    function tamila_tienda_carro_pasarelas_ajax(){
        global $wpdb;
       
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas where id='".sanitize_text_field($_POST['id'])."';", ARRAY_A); 
       
        ?>
        <div class="row">
            <form action="" method="POST" name="tamila_tienda_form_pasarela">
                <div class="mb-3">
                <label for="estado_id" class="form-label">Estado:</label>
                <select name="estado_id" id="estado_id" class="form-control">
                    <option value="1" <?php echo ($datos[0]['estado_id']=='0') ? '':'selected="true"' ?>>Activado</option>
                    <option value="0" <?php echo ($datos[0]['estado_id']=='1') ? '':'selected="true"' ?>>Apagado</option>
                </select>
                </div>
                <div class="mb-3">
                <label for="url" class="form-label">URL:</label>
                <textarea name="url" id="url" class="form-control" placeholder="URL"><?php echo $datos[0]['url'];?></textarea>
                </div>
                <div class="mb-3">
                <label for="cliente_id" class="form-label">Cliente ID:</label>
                <textarea name="cliente_id" id="cliente_id" class="form-control" placeholder="Cliente ID"><?php echo $datos[0]['cliente_id'];?></textarea>
                </div>
                <div class="mb-3">
                <label for="cliente_secret" class="form-label">Cliente Secret:</label>
                <textarea name="cliente_secret" id="cliente_secret" class="form-control" placeholder="Cliente Secret"><?php echo $datos[0]['cliente_secret'];?></textarea>
                </div>
                <hr/>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
                <input type="hidden" name="action" value="pasarela-edit" />
                <input type="hidden" name="return" value="" />
                <input type="hidden" name="pasarela_id" value="<?php echo sanitize_text_field($_POST['id'])?>" />
                <a href="javascript:void(0);" class="btn btn-primary" title="Editar" onclick="edit_pasarela();"><i class="fas fa-edit"></i> Editar</a>
            </form>
        </div>
        <?php
        die();
    }
    add_action('wp_ajax_tamila_tienda_pasarelas_ajax','tamila_tienda_carro_pasarelas_ajax');
}