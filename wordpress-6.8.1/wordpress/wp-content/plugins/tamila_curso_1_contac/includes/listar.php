<?php
if(!defined('ABSPATH')) die();
global $wpdb;
$tabla="{$wpdb->prefix}tamila_curso_1_contact";
$table2="{$wpdb->prefix}tamila_curso_1_contact_respuestas";
if(isset($_POST['nonce'])){
    switch($_POST['accion']){
        case '1':
            $data=[
                'nombre'=>sanitize_text_field($_POST['nombre']),
                'correo'=>sanitize_text_field($_POST['correo']),
                'fecha'=>date('Y-m-d')
            ];
            $wpdb->insert($tabla, $data);
            ?>
            <script>
                  Swal.fire({
                      icon: 'success',
                      title: 'OK',
                      text: 'Se creó el registro exitosamente',
                  });
                  setInterval(() => {
                    window.location=location.href;
                  }, 5000);
              </script>
            <?php
        break;
        case '2':
            $data = [
        
                'nombre' => sanitize_text_field($_POST['nombre']),
                'correo' => sanitize_text_field($_POST['correo'])
            ]; 
            $wpdb->update($tabla, $data, array('id'=>$_POST['id']));
            ?>
            <script>
                  Swal.fire({
                      icon: 'success',
                      title: 'OK',
                      text: 'Se modificó el registro exitosamente',
                  });
                  setInterval(() => {
                    window.location=location.href;
                  }, 5000);
              </script>
            <?php
        break;
        case '3':
            $wpdb->delete($tabla2, array('tamila_curso_1_contact_id' =>$_POST['id']));
            $wpdb->delete($tabla,array('id' =>$_POST['id']));
            ?>
            <script>
                  Swal.fire({
                      icon: 'success',
                      title: 'OK',
                      text: 'Se eliminó el registro exitosamente. Recuerda quitar el shortcode de las páginas en donde los estés usando',
                  });
                  setInterval(() => {
                    window.location=location.href;
                  }, 7000);
              </script>
            <?php
        break;
    }
    
}
//$datos=$wpdb->get_results("select * from {$tabla}", ARRAY_A);
$datos=$wpdb->get_results("select * from {$tabla} order by id desc;");
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
            <h1 class="wp-heading-inline"><?php echo get_admin_page_title();?></h1>
            <p class="d-flex justify-content-end">
                <a href="javascript:void(0);" class="btn btn-primary" title="Crear" onclick="get_crear_formulario('1', 'Crear nuevo formulario', '', '', '');"><i class="fas fa-plus"></i> Crear</a>
            </p>
            <hr/>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>E-Mail</th>
                            <th>Shortcode</th>
                            <th>Respuestas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($datos as $dato){
                            ?>
                        <tr>
                            <td><?php echo $dato->id;?></td>
                            <td><?php echo $dato->nombre;?></td>
                            <td><?php echo $dato->correo;?></td>
                            <td style="text-align:center;">[tamila_curso_1_contact id=<?php echo $dato->id;?>]</td>
                            <td style="text-align:center;">
                                <a href="javascript:void(0);" onclick="get_respuestas_formulario('<?php echo $dato->id;?>');"><i class="fas fa-search"></i></a>
                            </td>
                            <td style="text-align:center;">
                                <a href="javascript:void(0);" onclick="get_crear_formulario('2', 'Editar formulario N°<?php echo $dato->id;?>', '<?php echo $dato->nombre;?>', '<?php echo $dato->correo;?>', '<?php echo $dato->id;?>');"><i class="fas fa-edit"></i></a>
                                <a href="javascript:void(0);" onclick="get_eliminar_formulario('<?php echo $dato->id;?>');"><i class="fas fa-trash"></i></a>
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
<!--crear formulario-->
<div class="modal fade" id="crear_formulario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="crear_formulario_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <form action="" method="POST" name="tamila_curso_1_contact_form_crear">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" name="nombre" id="tamila_curso_1_contact_nombre" class="form-control" placeholder="Nombre" /> 
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">E-Mail:</label>
                    <input type="text" name="correo" id="tamila_curso_1_contact_correo" class="form-control" placeholder="E-Mail" /> 
                </div>
                <hr/>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
                <input type="hidden" name="id" id="tamila_curso_1_contact_id" />
                <input type="hidden" name="accion" id="tamila_curso_1_contact_accion" />
                <a href="javascript:void(0);" title="Enviar" class="btn btn-warning" onclick="tamila_curso_1_contact();"><i class="fas fa-plus"></i> Enviar</a>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/crear formulario-->
<!--respuestas-->
<div class="modal fade" id="respuesta_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="respuesta_modal_title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="respuesta_modal_body">
 
      </div>
      
    </div>
  </div>
</div>

<!--/respuestas-->
<!--eliminar-->
<form action="" name="tamila_curso_1_contac_form_eliminar" method="POST">
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
<input type="hidden" name="accion" id="tamila_curso_1_contac_form_eliminar_que" />
    <input type="hidden" name="id" id="tamila_curso_1_contac_form_eliminar_id" />
</form>
<!--//eliminar-->
