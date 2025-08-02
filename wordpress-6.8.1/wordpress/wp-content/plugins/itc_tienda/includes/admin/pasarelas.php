<?php 
if(!defined('ABSPATH')) die();
global $wpdb;
$datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas;", ARRAY_A); 
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
            <h1 class="wp-heading-inline">ITC tienda - Pasarelas de pago</h1>
            <hr/>
            <div class="table-responsive">
                <table class="wp-list-table widefat fixed striped table-view-list pages">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>URL</th>
                            <th>Cliente id</th>
                            <th>Cliente secret</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($datos as $dato){
                            ?>
                        <tr>
                            <td><?php echo ($dato['estado_id']==0) ? '<span class="text text-danger">Apagado</span>':'<span class="text text-success">Activada</span>';?></td>
                            <td><?php echo $dato['nombre'];?></td>
                            <td><?php echo $dato['descripcion'];?></td>
                            <td><?php echo $dato['url'];?></td>
                            <td><?php echo $dato['cliente_id'];?></td>
                            <td><?php echo $dato['cliente_secret'];?></td>
                            <td style="text-align:center;">
                            <a href="javascript:void(0);" title="Editar" onclick="get_pasarelas('<?php echo $dato['id'];?>', '<?php echo $dato['nombre'];?>');"><i class="fas fa-edit"></i></a>
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
<!--editar-->
<div class="modal fade" id="itc_tienda_pasarelas_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itc_tienda_pasarelas_modal_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="itc_tienda_pasarelas_modal_body">
        
      </div>
      
    </div>
  </div>
</div>

<!--/editar-->