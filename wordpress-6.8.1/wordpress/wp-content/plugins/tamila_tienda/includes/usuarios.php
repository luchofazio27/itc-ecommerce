<?php 
if(!defined('ABSPATH')) die();
if(!function_exists('tamila_tienda_usuarios_add')){
    function tamila_tienda_usuarios_add($user){
        $array = get_user_meta($user->ID, 'tamila_tienda_verificacion', true);
        if(isset($array) && is_array($array)){
            extract($array, EXTR_OVERWRITE);
        }else{
            $verificacion='';
        } 
        $html="
        <h3> Verificación </h3>
        <table class='form-table'>            
            <tr class='form-required '>
                <th scope='row'><label for='verificacion'>Verificación</label></th>
                <td>
                    
                <select class='regular-text' name='tamila_tienda_verificacion[verificacion]' id='verificacion'>
                <option value='0' ".(($verificacion=='0')?'selected="true"':'').">Por verificar</option>
                <option value='1' ".(($verificacion=='1')?'selected="true"':'').">Verificado</option>
            </select>
                                   
                </td>
            </tr>                
        </table>
    ";
        echo $html;
    }
    add_action( 'user_new_form',  'tamila_tienda_usuarios_add' );
    add_action( 'show_user_profile', 'tamila_tienda_usuarios_add' );
    add_action( 'edit_user_profile', 'tamila_tienda_usuarios_add' );
}
if(!function_exists('tamila_tienda_usuarios_editar')){
    function tamila_tienda_usuarios_editar($user_id ){
        if( !current_user_can( 'edit_user' ) ) {
            return;
        }
        if(isset($_POST['tamila_tienda_verificacion'])){
            $_POST['tamila_tienda_verificacion']['verificacion']=sanitize_text_field( $_POST['tamila_tienda_verificacion']['verificacion'] );
            update_user_meta( $user_id, 'tamila_tienda_verificacion', $_POST['tamila_tienda_verificacion'] );
        }
    }
}
add_action( 'user_register', 'tamila_tienda_usuarios_editar' );
add_action( 'personal_options_update', 'tamila_tienda_usuarios_editar' );
add_action( 'edit_user_profile_update', 'tamila_tienda_usuarios_editar' );