<?php
if(!defined('ABSPATH')) die();
global $wpdb;
$tabla = "{$wpdb->prefix}itc_tienda_slide"; 
if(isset($_POST['nonce'])){
  
    switch(sanitize_text_field($_POST["accion"])){
        case '1':
            $datos = [ 
                'nombre' => substr($_POST['itc_productos_galeria_agregar_foto_url'],strlen(get_site_url()), strlen($_POST['itc_productos_galeria_agregar_foto_url'])),
                'foto_id' => sanitize_text_field($_POST['itc_productos_galeria_agregar_foto_foto_id']) 
            ]; 
              $wpdb->insert($tabla,$datos);
              ?>
              <script>
                  Swal.fire({
                      icon: 'success',
                      title: 'OK',
                      text: 'Se creó el registro exitosamente',
                  });
                  window.location=location.href;
              </script>
                        <?php
        break;
        case '3':
             
            
            $wpdb->delete($tabla,array('id' =>$_POST['itc_productos_galeria_agregar_foto_foto_id'])); 
            ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'OK',
                    text: 'Se eliminó el registro exitosamente',
                });
                window.location=location.href;
            </script>
                      <?php
        break;
            
    }
}
$query="select * from {$tabla} order by id desc;";
$datos=$wpdb->get_results($query, ARRAY_A);
?>
<div class="wrap">
<div class="container-fluid">
        <div class="row">
<h1 class="wp-heading-inline"><?php echo get_admin_page_title()?></h1>
<hr />
<p><strong>Shortcode</strong>: [itc_tienda_slide]</p>
<hr/>
<p class="d-flex justify-content-end"> <a href="javascript:void(0);" class="btn btn-primary btnMarco" title="Crear"><i class="fas fa-plus"></i> Crear</a></p>
<hr/>
<div class="table-responsive">
<table class="table table-bordered table-striped table-hover">    
<thead>
        <tr>
            <th>Foto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($datos as $dato){
            ?>
            <tr>
            <td style="text-align:center;">
      
            <img src="<?php echo get_site_url().$dato['nombre'];?>" style=" height:200px" />       
    </td>
        <td style="text-align:center;">
               
                <a href="javascript:void(0);" title="Eliminar" onclick="get_eliminar_foto_slide( '<?php echo $dato['id'];?>');"><i class="fas fa-trash"></i></a>
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
<!--agregar-->
<form action="" name="itc_productos_galeria_agregar_foto" method="POST">
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
<input type="hidden" name="accion" value="1" /> 
    <input type="hidden" name="itc_productos_galeria_agregar_foto_foto_id" />
    <input type="hidden" name="itc_productos_galeria_agregar_foto_url" />
</form>
<!--//agregar-->