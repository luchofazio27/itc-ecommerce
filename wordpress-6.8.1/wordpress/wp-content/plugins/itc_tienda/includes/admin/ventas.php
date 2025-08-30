<?php 
// Seguridad: evitamos acceso directo al archivo
if(!defined('ABSPATH')) die();

// Conexión global a la base de datos de WordPress
global $wpdb;

// Verificamos si se están aplicando filtros (estado, pasarela, cliente)
if(isset($_GET['filtro']) and isset($_GET['filtro_tipo']) and isset($_GET['filtro_valor'])){
    // Determinamos el tipo de filtro
    switch(sanitize_text_field( $_GET['filtro_tipo'] )){
        case 'estado':
            // Filtro por estado del pedido
            $where="
            where 
            {$wpdb->prefix}itc_tienda_carro.estado_id ='".sanitize_text_field( $_GET['filtro_valor'] )."'
            ";
            $titulo="ITC tienda - por estado";
        break;
        case 'pasarela':
            // Filtro por pasarela de pago
            $where="
            where 
            {$wpdb->prefix}itc_tienda_carro.tipo_pago ='".sanitize_text_field( $_GET['filtro_valor'] )."'
            ";
            $titulo="ITC tienda - por pasarela de pago";
        break;
        case 'cliente':
            // Filtro por cliente (usuario)
            $where="
            where 
            {$wpdb->prefix}itc_tienda_carro.usuario_id ='".sanitize_text_field( $_GET['filtro_valor'] )."'
            ";
            $titulo="ITC tienda - por cliente";
        break;
    }
}else{
    // Si no hay filtros, mostramos todas las ventas
    $titulo="ITC tienda - Ventas";
    $where="";
}

// Obtenemos los datos de ventas desde la base de datos
$datos=$wpdb->get_results("select 
{$wpdb->prefix}itc_tienda_carro.id, 
{$wpdb->prefix}itc_tienda_carro.fecha, 
{$wpdb->prefix}itc_tienda_carro_estado.nombre as estado, 
{$wpdb->prefix}itc_tienda_carro.estado_id,
{$wpdb->prefix}itc_tienda_carro.direccion,
{$wpdb->prefix}itc_tienda_carro.observaciones,
{$wpdb->prefix}itc_tienda_carro.telefono,
{$wpdb->prefix}itc_tienda_carro.ciudad,
{$wpdb->prefix}itc_tienda_carro.tipo_pago,
{$wpdb->prefix}itc_tienda_carro.monto,
{$wpdb->prefix}users.user_email, 
{$wpdb->prefix}users.display_name ,
{$wpdb->prefix}itc_tienda_carro_pasarelas.nombre as pasarela,
{$wpdb->prefix}itc_tienda_carro.usuario_id
from 
{$wpdb->prefix}itc_tienda_carro 
inner join {$wpdb->prefix}itc_tienda_carro_estado on {$wpdb->prefix}itc_tienda_carro_estado.id={$wpdb->prefix}itc_tienda_carro.estado_id 
inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}itc_tienda_carro.usuario_id
inner join {$wpdb->prefix}itc_tienda_carro_pasarelas on {$wpdb->prefix}itc_tienda_carro_pasarelas.id={$wpdb->prefix}itc_tienda_carro.tipo_pago
".$where."
order by {$wpdb->prefix}itc_tienda_carro.id desc;
"); 
?>

<!-- Estructura HTML para mostrar las ventas en el panel de administración -->
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
        <hr/>
          <?php 
            // Mostramos mensaje de éxito si se modificó un registro
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
                <!-- Título dinámico según el filtro -->
                <h1 class="wp-heading-inline"><?php echo $titulo;?></h1>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <!-- Menú de filtros -->
                    <div class="dropdown">
                  <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton1" title="Opciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-list"></i> Opciones </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                  <li>
                      <!-- Opción ver todas las ventas -->
                      <a class="dropdown-item" href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php";?>" title="Todos" >
                        <i class="fas fa-search"></i> Todos </a>
                    </li>  
                  <li>
                      <!-- Opción filtrar por estado -->
                      <a class="dropdown-item" href="javascript:void(0);" title="Estado" onclick="get_filtro_venta('estado','Estado');">
                        <i class="fas fa-search"></i> Estado </a>
                    </li>
                    <li>
                      <!-- Opción filtrar por pasarela -->
                    <a class="dropdown-item" href="javascript:void(0);" title="Pasarelas" onclick="get_filtro_venta('pasarela','Pasarelas');">
                        <i class="fas fa-search"></i> Pasarelas </a>
                    </li>
                    <li>
                      <!-- Opción filtrar por cliente -->
                    <a class="dropdown-item" href="javascript:void(0);" title="Cliente" onclick="get_filtro_venta('cliente','Clientes');">
                        <i class="fas fa-search"></i> Clientes </a>
                    </li>
                    
                    <li>
                      <!-- Exportar a Excel -->
                      <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fexcel.php&excel=1";?>" class="dropdown-item" title="Exportar a excel">
                        <i class="fas fa-file-excel"></i> Exportar a excel </a>
                    </li>
                  </ul>
                </div>  
                </div>
            </div>
            <hr/>

            <!-- Tabla de ventas -->
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
                // Recorremos cada venta y la mostramos en la tabla
                foreach($datos as $dato){
                    ?>
                <tr>
                    <!-- ID de la venta -->
                    <td><?php echo $dato->id;?></td>
                    
                    <!-- Nombre del cliente con enlace -->
                    <td> 
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&usuario_id=".$dato->usuario_id;?>" 
                           title="<?php echo $dato->display_name;?>"><?php echo $dato->display_name?></a>
                    </td>
                    
                    <!-- Teléfono del cliente -->
                    <td><?php echo  $dato->telefono; ?></td>
                    
                    <!-- Email del cliente -->
                    <td><?php echo $dato->user_email?></td>
                    
                    <!-- Dirección del cliente -->
                    <td><?php echo  $dato->direccion; ?></td>
                    
                    <!-- Observaciones -->
                    <td><?php echo  $dato->observaciones; ?></td> 
                    
                    <!-- Fecha de la venta -->
                    <td><?php $date = date_create($dato->fecha);echo date_format($date, 'd/m/Y'); ?></td>
                    
                    <!-- Pasarela de pago -->
                    <td>
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&tipo_pago=".$dato->tipo_pago;?>" 
                           title="<?php echo $dato->display_name;?>"><?php echo $dato->pasarela;?></a>
                    </td>
                    
                    <!-- Monto total -->
                    <td>$<?php echo number_format($dato->monto, 0, '', '.');?></td>
                    
                    <!-- Estado con badges de colores -->
                    <td>
                      <?php 
                      switch($dato->estado_id){
                        case '1':
                          ?>
                        <div class="badge bg-primary text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                        case '2':
                          ?>
                        <div class="badge bg-secondary text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                        case '3':
                          ?>
                        <div class="badge bg-warning text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                        case '4':
                          ?>
                        <div class="badge bg-success text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                        case '5':
                          ?>
                        <div class="badge bg-danger text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                        case '6':
                          ?>
                        <div class="badge bg-primary text-wrap" style="width: 5rem;">
                        <a href="<?php echo get_site_url()."/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php&filtro=1&estado_id=".$dato->estado_id;?>" 
                           style="color:#fff;" title="<?php echo $dato->estado;?>"><?php echo $dato->estado;?></a>
                        </div>
                          <?php
                        break;
                      }
                      ?>
                    </td>
                    
                    <!-- Botón detalle -->
                    <td style="text-align:center;">
                      <a href="javascript:void(0);" title="Detalle venta N° <?php echo $dato->id?>" 
                         onclick="get_detalle_venta('<?php echo $dato->id;?>', '<?php echo $dato->id;?>');"><i class="fas fa-search"></i></a>
                    </td>
                    
                    <!-- Botón PDF -->
                    <td style="text-align:center;">
                      <a href="<?php echo get_site_url()."/orden-de-venta/?pdf=".$dato->id;?>" 
                         title="Orden de venta N° <?php echo $dato->id?>" target="_blank"><i class="fas fa-file-pdf"></i></a>
                    </td>
                    
                    <!-- Botón editar -->
                    <td style="text-align:center;">
                      <a href="javascript:void(0);" title="Editar" 
                         onclick="get_editar_venta('<?php echo $dato->id;?>', '<?php echo $dato->id;?>');"><i class="fas fa-edit"></i></a>
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

<!-- Modal para detalle y edición de ventas -->
<div class="modal fade" id="itc_tienda_ventas_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itc_tienda_ventas_modal_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Cerrar"></button>
      </div>
      <div class="modal-body" id="itc_tienda_ventas_modal_body">
        
      </div>
    </div>
  </div>
</div>
<!--/modal-->
