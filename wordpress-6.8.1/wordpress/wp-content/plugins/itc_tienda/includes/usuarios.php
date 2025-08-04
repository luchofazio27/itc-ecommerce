<?php 
if(!defined('ABSPATH')) die();
if(!function_exists('itc_tienda_usuarios_add')){
    function itc_tienda_usuarios_add($user){
        $array = get_user_meta($user->ID, 'itc_tienda_verificacion', true); // obtenemos los metadatos del usuario
        if(isset($array) && is_array($array)){
            extract($array, EXTR_OVERWRITE); // extrae las variables del array
        }else{
            $verificacion='';
        } 
        $html="
        <h3> Verificación </h3>
        <table class='form-table'>            
            <tr class='form-required '>
                <th scope='row'><label for='verificacion'>Verificación</label></th>
                <td>
                    
                <select class='regular-text' name='itc_tienda_verificacion[verificacion]' id='verificacion'>
                <option value='0' ".(($verificacion=='0')?'selected="true"':'').">Por verificar</option>
                <option value='1' ".(($verificacion=='1')?'selected="true"':'').">Verificado</option>
            </select>
                                   
                </td>
            </tr>                
        </table>
    ";
        echo $html;
    }
    add_action( 'user_new_form',  'itc_tienda_usuarios_add' );
    add_action( 'show_user_profile', 'itc_tienda_usuarios_add' );
    add_action( 'edit_user_profile', 'itc_tienda_usuarios_add' );
}
if(!function_exists('itc_tienda_usuarios_editar')){
    function itc_tienda_usuarios_editar($user_id ){
        if( !current_user_can( 'edit_user' ) ) { // verifica si el usuario actual tiene permisos para editar
            return;
        }
        if(isset($_POST['itc_tienda_verificacion'])){
            $_POST['itc_tienda_verificacion']['verificacion']=sanitize_text_field( $_POST['itc_tienda_verificacion']['verificacion'] );
            update_user_meta( $user_id, 'itc_tienda_verificacion', $_POST['itc_tienda_verificacion'] );
        }
    }
}
add_action( 'user_register', 'itc_tienda_usuarios_editar' );
add_action( 'personal_options_update', 'itc_tienda_usuarios_editar' );
add_action( 'edit_user_profile_update', 'itc_tienda_usuarios_editar' );