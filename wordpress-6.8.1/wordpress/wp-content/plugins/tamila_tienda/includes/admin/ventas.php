<?php 
if(!defined('ABSPATH')) die();
global $wpdb;

if(isset($_GET['filtro']) and isset($_GET['filtro_tipo']) and isset($_GET['filtro_valor'])){
    switch(sanitize_text_field( $_GET['filtro_tipo'] )){
        case 'estado':
            $where="
            where 
            {$wpdb->prefix}tamila_tienda_carro.estado_id ='".sanitize_text_field( $_GET['filtro_valor'] )."'
          
            ";
            $titulo="Tamila tienda - por estado";
        break;
        case 'pasarela':
            $where="
            where 
            {$wpdb->prefix}tamila_tienda_carro.tipo_pago ='".sanitize_text_field( $_GET['filtro_valor'] )."'

            ";
            $titulo="Tamila tienda - por pasarela de pago";
        break;
        case 'cliente':
            $where="
            where 
            {$wpdb->prefix}tamila_tienda_carro.usuario_id ='".sanitize_text_field( $_GET['filtro_valor'] )."'

            ";
            $titulo="Tamila tienda - por cliente";
        break;
    }
}else{
    $titulo="Tamila tienda - Ventas";
    $where="";
}

$datos=$wpdb->get_results("select 
{$wpdb->prefix}tamila_tienda_carro.id, 
{$wpdb->prefix}tamila_tienda_carro.fecha, 
{$wpdb->prefix}tamila_tienda_carro_estado.nombre as estado, 
{$wpdb->prefix}tamila_tienda_carro.estado_id,
{$wpdb->prefix}tamila_tienda_carro.direccion,
{$wpdb->prefix}tamila_tienda_carro.observaciones,
{$wpdb->prefix}tamila_tienda_carro.telefono,
{$wpdb->prefix}tamila_tienda_carro.ciudad,
{$wpdb->prefix}tamila_tienda_carro.tipo_pago,
{$wpdb->prefix}tamila_tienda_carro.monto,
{$wpdb->prefix}users.user_email, 
{$wpdb->prefix}users.display_name ,
{$wpdb->prefix}tamila_tienda_carro_pasarelas.nombre as pasarela,
{$wpdb->prefix}tamila_tienda_carro.usuario_id
from 
{$wpdb->prefix}tamila_tienda_carro 
inner join {$wpdb->prefix}tamila_tienda_carro_estado on {$wpdb->prefix}tamila_tienda_carro_estado.id={$wpdb->prefix}tamila_tienda_carro.estado_id 
inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}tamila_tienda_carro.usuario_id
inner join {$wpdb->prefix}tamila_tienda_carro_pasarelas on {$wpdb->prefix}tamila_tienda_carro_pasarelas.id={$wpdb->prefix}tamila_tienda_carro.tipo_pago
".$where."
order by {$wpdb->prefix}tamila_tienda_carro.id desc;
        "); 
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
        <hr/>
          <?php 
            if(isset( $_REQUEST["msg"] ) and sanitize_text_field( $_REQUEST['msg'] )=='1'){
                ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            Se modificó el registro exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="col-6">
                <h1 class="wp-heading-inline"><?php echo $titulo;?></h1>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <!--filtros-->
                    <div class="dropdown">
                  <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1" title="Opciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-list"></i> Opciones </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                  <li>
                      <a class="dropdown-item" href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php";?>" title="Todos" >
                        <i class="fas fa-search"></i> Todos </a>
                    </li>  
                  <li>
                      <a class="dropdown-item" href="javascript:void(0);" title="Estado" onclick="get_filtro_venta('estado','Estado');">
                        <i class="fas fa-search"></i> Estado </a>
                    </li>
                    <li>
                    <a class="dropdown-item" href="javascript:void(0);" title="Pasarelas" onclick="get_filtro_venta('pasarela','Pasarelas');">
                        <i class="fas fa-search"></i> Pasarelas </a>
                    </li>
                    <li>
                    <a class="dropdown-item" href="javascript:void(0);" title="Cliente" onclick="get_filtro_venta('cliente','Clientes');">
                        <i class="fas fa-search"></i> Clientes </a>
                    </li>
                    
                    <li>
                      <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fexcel.php&excel=1";?>" class="dropdown-item" title="Exportar a excel">
                        <i class="fas fa-file-excel"></i> Exportar a excel </a>
                    </li>
                  </ul>
                </div>  
                    <!--/filtros-->
                </div>
            </div>
            <hr/>
            <div class="table-responsive">
            <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                <th>N° </th> 
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>E-Mail</th>
                <th>Dirección</th>
                <th>Observaciones</th>
                <th>Fecha</th> 
                <th>Pasarela</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Detalle</th>
                <th>PDF</th>
                <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($datos as $dato){
                    ?>
                <tr>
                    <td><?php echo $dato->id;?></td>
                    <td> <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&usuario_id=".$dato->usuario_id;?>" title="<?php echo $dato->display_name;?>"><?php echo $dato->display_name?></a></td>
                <td><?php echo  $dato->telefono; ?></td>
                <td><?php echo $dato->user_email?></td>
                <td><?php echo  $dato->direccion; ?></td>
                <td><?php echo  $dato->observaciones; ?></td> 
                <td><?php $date = date_create($dato->fecha);echo date_format($date, 'd/m/Y'); ?></td>
                <td><a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&tipo_pago=".$dato->tipo_pago;?>" title="<?php echo $dato->display_name;?>"><?php echo $dato->pasarela;?></a></td>
                <td>$<?php echo number_format($dato->monto, 0, '', '.');?></td>
                <td>
                  <?php 
                  switch($dato->estado_id){
                    case '1':
                      ?>
                    <div class="badge bg-primary text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                    case '2':
                      ?>
                    <div class="badge bg-secondary text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                    case '3':
                      ?>
                    <div class="badge bg-warning text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                    case '4':
                      ?>
                    <div class="badge bg-success text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                    case '5':
                      ?>
                    <div class="badge bg-danger text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                    case '6':
                      ?>
                    <div class="badge bg-primary text-wrap" style="width: 5rem;">
                    <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                    </div>
                      <?php
                    break;
                   
                  }
                  ?>
                </td>
                <td style="text-align:center;">
                <a href="javascript:void(0);" title="Detalle venta N° <?php echo $dato->id?>" onclick="get_detalle_venta('<?php echo $dato->id;?>', '<?php echo $dato->id;?>');"><i class="fas fa-search"></i></a>
                </td>
                <td style="text-align:center;">
                <a href="<?php echo get_site_url()."/orden-de-venta/?pdf=".$dato->id;?>" title="Orden de venta N° <?php echo $dato->id?>" target="_blank"><i class="fas fa-file-pdf"></i></a>
                </td>
                <td style="text-align:center;">
                <a href="javascript:void(0);" title="Editar" onclick="get_editar_venta('<?php echo $dato->id;?>', '<?php echo $dato->id;?>');"><i class="fas fa-edit"></i></a>
                </td>
                </tr>
                    <?php
                }
                ?>
            </tbody>
            </table> 
            </div>
        </div>
    </div>
</div>
 <!--modal-->
 <div class="modal fade" id="tamila_tienda_ventas_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tamila_tienda_ventas_modal_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Cerrar"></button>
      </div>
      <div class="modal-body" id="tamila_tienda_ventas_modal_body">
        
      </div>
      
    </div>
  </div>
</div>
<!--/modal-->