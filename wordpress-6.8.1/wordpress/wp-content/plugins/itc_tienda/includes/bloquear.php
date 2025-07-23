<?php 
if(!defined('ABSPATH')) die();
add_action('init', function(){
    if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) 
    {
         wp_redirect( home_url('error') ); exit;
         //wp_redirect equivalente o muy parecido a usar header location 
        } 
});
if(!function_exists('itc_api_authentication_errors')){
    function itc_api_authentication_errors($result){
        if(!empty($result)){
            return $result;
        }
        return new WP_Error( 
            'rest_not_logged_in', 
            'No autorizado', 
            array( 'status' => 401 ) 
        );
        
    }
    add_filter('rest_authentication_errors', 'itc_api_authentication_errors');
}