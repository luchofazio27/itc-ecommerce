<?php 
if(!defined('ABSPATH')) die();
use Dompdf\Dompdf; 
//ajax detalle venta
add_action('wp_ajax_tamila_tienda_ventas_ajax', function(){
    global $wpdb;
         
    $datos=$wpdb->get_results("select 
{$wpdb->prefix}tamila_tienda_carro.id, 
{$wpdb->prefix}tamila_tienda_carro.fecha, 
{$wpdb->prefix}tamila_tienda_carro.monto,
{$wpdb->prefix}tamila_tienda_carro_estado.nombre as estado, 
{$wpdb->prefix}tamila_tienda_carro.estado_id, 
{$wpdb->prefix}posts.post_title, 
{$wpdb->prefix}posts.post_name,
{$wpdb->prefix}tamila_tienda_carro_detalle.cantidad,
{$wpdb->prefix}tamila_tienda_carro_detalle.producto_id,
{$wpdb->prefix}tamila_tienda_carro.direccion,
{$wpdb->prefix}tamila_tienda_carro.observaciones,
{$wpdb->prefix}tamila_tienda_carro.telefono,
{$wpdb->prefix}tamila_tienda_carro.ciudad,
{$wpdb->prefix}tamila_tienda_carro.tipo_pago,
{$wpdb->prefix}users.user_email, 
{$wpdb->prefix}users.display_name
from 
{$wpdb->prefix}tamila_tienda_carro_detalle 
inner join {$wpdb->prefix}tamila_tienda_carro on {$wpdb->prefix}tamila_tienda_carro.id={$wpdb->prefix}tamila_tienda_carro_detalle.tamila_tienda_carro_id 
inner join {$wpdb->prefix}tamila_tienda_carro_estado on {$wpdb->prefix}tamila_tienda_carro_estado.id={$wpdb->prefix}tamila_tienda_carro.estado_id 
inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}tamila_tienda_carro_detalle.producto_id  
inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}tamila_tienda_carro.usuario_id
where {$wpdb->prefix}tamila_tienda_carro.id ='".sanitize_text_field($_POST['id'])."';
    "); 
    ?>
    <div class="row">
    <hr/>
   
    <div class="table-responsive">
    <table class="wp-list-table widefat fixed striped table-view-list pages">    
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>E-Mail</th>
                <th>Dirección</th>
                <th>Observaciones</th>
                <th>Fecha</th>
                <th>Monto total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $datos[0]->display_name?></td>
                <td><?php echo  $datos[0]->telefono; ?></td>
                <td><?php echo $datos[0]->user_email?></td>
                <td><?php echo  $datos[0]->direccion; ?></td>
                <td><?php echo  $datos[0]->observaciones; ?></td>
                <td><?php $date = date_create($datos[0]->fecha);echo  date_format($date, 'd/m/Y'); ?></td>
                <td>$<?php echo number_format($datos[0]->monto, 0, '', '.');?></td>
                <td>
                    <?php
                switch($datos[0]->estado_id){
                
                    case '1':
                        ?>
                      <div class="badge bg-primary text-wrap" style="width: 5rem;">
                      <?php echo $datos[0]->estado;?>
                      </div>
                        <?php
                      break;
                      case '2':
                        ?>
                      <div class="badge bg-secondary text-wrap" style="width: 5rem;">
                      <?php echo $datos[0]->estado;?>
                      </div>
                        <?php
                      break;
                case '3':
                  ?>
                <div class="badge bg-warning text-wrap" style="width: 5rem;">
                  <?php echo $datos[0]->estado;?>
                </div>
                  <?php
                break;
                case '4':
                  ?>
                <div class="badge bg-success text-wrap" style="width: 5rem;">
                  <?php echo $datos[0]->estado;?>
                </div>
                  <?php
                break;
                case '5':
                  ?>
                <div class="badge bg-danger text-wrap" style="width: 5rem;">
                  <?php echo $datos[0]->estado;?>
                </div>
                  <?php
                break;
                case '6':
                    ?>
                  <div class="badge bg-primary text-wrap" style="width: 5rem;">
                  <?php echo $datos[0]->estado;?>
                  </div>
                    <?php
                  break;
              }
              ?>    
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <hr/>
    <h4>Detalle</h4>
    <div class="table-responsive">
    <table class="wp-list-table widefat fixed striped table-view-list pages">    
<thead>
    <tr> 
      <th>Foto</th>
      <th>Producto</th>
      <th>Cantidad</th>
      <th>Monto</th>
       
    </tr>
</thead>
<tbody>
    <?php 
    $sum=0;
    foreach($datos as $dato){
      $sum=$sum+get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad;
        ?>
        <tr> 
            <td style="text-align:center;">
              <a href="<?php echo get_site_url()."/".$dato->post_name?>" title="<?php echo $dato->post_title ?>" target="_blank">
                  <img class="img-fluid" style="width: 50px;height: 50px;" src="<?php echo  get_site_url().substr(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0],strlen(get_site_url()), strlen(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0])); ?>" alt="?php echo $compra->post_title ?>" />
              </a>
            </td>
            <td><?php echo $dato->post_title ?></td>
            <td><?php echo $dato->cantidad ?></td> 
            <td>$<?php echo number_format(get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad, 0, '', '.');?></td>
            
            
        </tr>
        <?php
    }   
    ?>
</tbody>
</table>
    </div>
    </div>
    <?php
    die();
});
//ajax editar
add_action('after_setup_theme', function(){
    if(isset($_POST['nonce']) and $_POST['action']=='venta-edit'){
        global $wpdb;
        $wpdb->query("update {$wpdb->prefix}tamila_tienda_carro set estado_id='".sanitize_text_field($_POST['estado_id'])."' where id='".sanitize_text_field($_POST['venta_id'])."' ");
        wp_safe_redirect( $_POST['return']."&msg=1" ); exit;
    }
});
add_action('wp_ajax_tamila_tienda_ventas_editar_ajax', function(){
    global $wpdb;
        
        $datos=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro where id='".sanitize_text_field($_POST['id'])."';");
        $estados=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_estado  ");
        ?>
        <div class="row">
            <form action="" method="POST" name="tamila_tienda_form_ventas">
                <div class="mb-3">
                <label for="estado_id" class="form-label">Estado:</label>
                <select name="estado_id" id="estado_id" class="form-control">
                    <?php
                    foreach($estados as $estado){
                        ?>
                        <option value="<?php echo $estado->id;?>" <?php echo ($estado->id==$datos[0]->estado_id)?'selected="true"':'';?>><?php echo $estado->nombre;?></option>
                        <?php
                    }
                    ?>
                </select>
                </div>
                
                 
                 
                <hr/>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
                <input type="hidden" name="action" value="venta-edit" />
                <input type="hidden" name="return" value="<?php echo (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>" />
                <input type="hidden" name="venta_id" value="<?php echo sanitize_text_field($_POST['id'])?>" />
                <a href="javascript:void(0);" class="btn btn-primary" title="Editar" onclick="send_editar_ventas();"><i class="fas fa-edit"></i> Editar</a>
            </form>
        </div>
        <?php
        die();
});
//filtros ajax
add_action('wp_ajax_tamila_tienda_ventas_filtro_ajax', function(){
    global $wpdb;
    switch(sanitize_text_field( $_POST['id'] )){
        case 'estado':
            $estados=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_estado  ");
            ?>
            <div class="row">
                <form action="" method="GET" name="form_filtro_venta">
                <div class="mb-3">
                        <label for="filtro_valor" class="form-label">Estado:</label>
                        <select name="filtro_valor" id="filtro_valor" class="form-control">
                            <?php
                            foreach($estados as $estado){
                                ?>
                                <option value="<?php echo $estado->id;?>"><?php echo $estado->nombre;?></option>
                                <?php
                            }
                            ?>
                        </select>
                </div>
                <hr />
                <input type="hidden" name="filtro" value="1" id="filtro" />
                <input type="hidden" name="filtro_tipo" value="estado" />
                <a href="javascript:void(0):" onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php');" class="btn btn-success" title="Buscar"><i class="fas fa-search"></i> Buscar</a>
                </form>
            </div>
            <?php
            die();
        break;
        case 'pasarela':
            $pasarelas=$wpdb->get_results("select * from {$wpdb->prefix}tamila_tienda_carro_pasarelas  ");
                ?>
                <div class="row">
                    <form action="" method="GET" name="form_filtro_venta"> 
                        <div class="mb-3">
                        <label for="filtro_valor" class="form-label">Pasarela:</label>
                        <select name="filtro_valor" id="filtro_valor" class="form-control">
                            <?php
                            foreach($pasarelas as $pasarela){
                                ?>
                                <option value="<?php echo $pasarela->id;?>"><?php echo $pasarela->nombre;?></option>
                                <?php
                            }
                            ?>
                        </select>
                        </div>
                        <hr />
                        <input type="hidden" name="filtro" value="1" id="filtro" />
                        <input type="hidden" name="filtro_tipo" value="pasarela" />
                        <a href="javascript:void(0):" onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php');" class="btn btn-success" title="Buscar"><i class="fas fa-search"></i> Buscar</a>
                    </form>
                </div>
                <?php
                die();
        break;
        case 'cliente':
            $clientes=$wpdb->get_results("select distinct({$wpdb->prefix}tamila_tienda_carro.usuario_id) as id, {$wpdb->prefix}users.display_name as nombre from 
                {$wpdb->prefix}tamila_tienda_carro 
                inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}tamila_tienda_carro.usuario_id
                 ; ");
                 ?>
                 <div class="row">
                     <form action="" method="GET" name="form_filtro_venta"> 
                         <div class="mb-3">
                         <label for="filtro_valor" class="form-label">Cliente:</label>
                         <select name="filtro_valor" id="filtro_valor" class="form-control">
                             <?php
                             foreach($clientes as $cliente){
                                 ?>
                                 <option value="<?php echo $cliente->id;?>"><?php echo $cliente->nombre;?></option>
                                 <?php
                             }
                             ?>
                         </select>
                         </div>
                         <hr />
                         <input type="hidden" name="filtro" value="1" id="filtro" />
                         <input type="hidden" name="filtro_tipo" value="cliente" />
                         <a href="javascript:void(0):" onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php');" class="btn btn-success" title="Buscar"><i class="fas fa-search"></i> Buscar</a>
                     </form>
                 </div>
                 <?php
                 die();   
        break;
    }
});


//creamos PDF
add_action('after_setup_theme', function(){
    if(isset($_GET['pdf']) and is_numeric($_GET['pdf'])){
        global $wpdb;
 
        $datos=$wpdb->get_results("select 
        {$wpdb->prefix}tamila_tienda_carro.id, 
        {$wpdb->prefix}tamila_tienda_carro.fecha, 
        {$wpdb->prefix}tamila_tienda_carro.monto,
        {$wpdb->prefix}tamila_tienda_carro_estado.nombre as estado, 
        
        {$wpdb->prefix}posts.post_title, 
        {$wpdb->prefix}posts.post_name,
        {$wpdb->prefix}tamila_tienda_carro_detalle.cantidad,
        {$wpdb->prefix}tamila_tienda_carro_detalle.producto_id,
        {$wpdb->prefix}tamila_tienda_carro.direccion,
        {$wpdb->prefix}tamila_tienda_carro.observaciones,
        {$wpdb->prefix}tamila_tienda_carro.telefono,
        {$wpdb->prefix}tamila_tienda_carro.ciudad,
        {$wpdb->prefix}tamila_tienda_carro.tipo_pago,
        {$wpdb->prefix}users.user_email, 
        {$wpdb->prefix}users.display_name
        from 
        {$wpdb->prefix}tamila_tienda_carro_detalle 
        inner join {$wpdb->prefix}tamila_tienda_carro on {$wpdb->prefix}tamila_tienda_carro.id={$wpdb->prefix}tamila_tienda_carro_detalle.tamila_tienda_carro_id 
        inner join {$wpdb->prefix}tamila_tienda_carro_estado on {$wpdb->prefix}tamila_tienda_carro_estado.id={$wpdb->prefix}tamila_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}tamila_tienda_carro_detalle.producto_id  
        inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}tamila_tienda_carro.usuario_id
        where {$wpdb->prefix}tamila_tienda_carro.id ='".sanitize_text_field($_GET['pdf'])."';
                "); 
        require 'vendor/autoload.php';
        $dompdf = new Dompdf(array('enable_remote' => true));
        $html='';
        $html.='<h1>Orden de venta N° '.sanitize_text_field($_GET['pdf']).'</h1>';
        $html.='<table border=1>    
        <thead>
            <tr>
                <th >Cliente</th>
                <th>Teléfono</th>
                <th>E-Mail</th>
                <th>Dirección</th>
                <th>Observaciones</th>
                <th>Fecha</th>
                <th>Monto total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>';
        $date = date_create($datos[0]->fecha);
        $html.='<tr>
        <td>'.$datos[0]->display_name.'</td>
        <td>'.$datos[0]->telefono.'</td>
        <td>'.$datos[0]->user_email.'</td>
        <td>'.$datos[0]->direccion.'</td>
        <td>'.$datos[0]->observaciones.'</td>
        <td>'.date_format($date, 'd/m/Y').'</td>
        <td>'.number_format($datos[0]->monto, 0, '', '.').'</td>
        <td>'.$datos[0]->estado.'</td>
    </tr>';
        $html.='</tbody></table>';
        $html.='<hr/><h4>Detalle</h4>';
        $html.='<table border=1>    
        <thead>
                <tr> 
                  <th>Foto</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Monto</th> 
                </tr>
            </thead>
            <tbody>';
        
        $sum=0;
        foreach($datos as $dato){
            $sum=$sum+get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad;
            $html.='<tr> 
            <td style="text-align:center;">
           
                  <img class="img-fluid" style="width: 50px;height: 50px;" src="'.get_site_url().substr(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0],strlen(get_site_url()), strlen(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0])).'"   />
               
            </td>
            <td>'.$dato->post_title.'</td>
            <td>'.$dato->cantidad.'</td> 
            <td>$'.number_format(get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad, 0, '', '.').'</td>
            
            
        </tr>';
        }
        $html.='</tbody></table>';
        $dompdf->loadHtml($html); 
        $dompdf->setPaper('A4', 'landscape'); 
        $dompdf->render();  
        return $dompdf->stream(time().'.pdf');
    }
});