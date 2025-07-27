<?php
if(!defined('ABSPATH')) die();
if(!isset($_GET['id']) or !isset($_GET['nombre'])){ // Verifica si el producto existe y te redirecciona a listar.php
    ?>
    <script>
        window.location='<?php echo get_site_url() ?>/wp-admin/admin.php?page=itc_galeria%2Fadmin%2Flistar.php';
    </script>
    <?php
}
$id=sanitize_text_field($_GET['id']); // Sanitiza el ID del producto
global $wpdb; // Accede a la base de datos de WordPress
$tabla = "{$wpdb->prefix}itc_tienda_productos_galeria";
if(isset($_POST['nonce'])){
    switch($_POST['accion']){
        case '1':
            $datos = [
                'post_id'=>$id,
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
                  setInterval(() => {
                    window.location=location.href;
                  }, 3000);
              </script>
                        <?php
        break;
        case '3':
            $wpdb->delete($tabla,array('id' =>sanitize_text_field($_POST['itc_productos_galeria_eliminar_foto_id']))); 
            ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'OK',
                    text: 'Se eliminó el registro exitosamente',
                });
                setInterval(() => {
                    window.location=location.href;
                  }, 3000);
            </script>
                      <?php
        break;
    }
}

$fotos=$wpdb->get_results("select * from {$tabla} where post_id='".$id."' order by id desc;", ARRAY_A); 
?>
<div class="wrap">
    <div class="container-fluid">
        <div class="row">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Flistar.php">Productos Galería</a></li> 
            <li class="breadcrumb-item active" aria-current="page">Fotos: <?php echo sanitize_text_field($_GET['nombre']);?></li>
        </ol>
        <h1 class="wp-heading-inline">Fotos: <?php echo sanitize_text_field($_GET['nombre']);?></h1>
        <p class="d-flex justify-content-end">
            <a href="javascript:void(0);" class="btn btn-primary btnMarco" title="Crear"><i class="fas fa-plus"></i> Agregar</a>
        </p>
        <div class="table responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($fotos as $foto){
                        ?>
                    <tr>
                        <td style="text-align:center;">
                        <img src="<?php echo get_site_url().$foto['nombre'];?>" style=" height:200px" />
                        </td>
                        <td style="text-align:center;">
                        <a href="javascript:void(0);" title="Eliminar" onclick="get_eliminar_foto_galeria('<?php echo $foto['id'];?>');"><i class="fas fa-trash"></i></a>
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
<!--eliminar-->
<form action="" name="itc_productos_galeria_eliminar_foto" method="POST">
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" id="nonce" />
<input type="hidden" name="accion" value="3" /> 
    <input type="hidden" name="itc_productos_galeria_eliminar_foto_id" /> 
</form>
<!--//eliminar-->