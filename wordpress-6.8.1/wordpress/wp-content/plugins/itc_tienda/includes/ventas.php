<?php 
// Seguridad: bloquea el acceso directo al archivo
if(!defined('ABSPATH')) die();

// Importamos la librería Dompdf para generar PDFs
use Dompdf\Dompdf; 

// ------------------------------
// AJAX DETALLE DE VENTA
// ------------------------------
add_action('wp_ajax_itc_tienda_ventas_ajax', function(){
    global $wpdb; // Acceso a la base de datos de WordPress
         
    // Consulta SQL: obtenemos los datos de una venta por su ID
    $datos=$wpdb->get_results("select 
        {$wpdb->prefix}itc_tienda_carro.id, 
        {$wpdb->prefix}itc_tienda_carro.fecha, 
        {$wpdb->prefix}itc_tienda_carro.monto,
        {$wpdb->prefix}itc_tienda_carro_estado.nombre as estado, 
        {$wpdb->prefix}itc_tienda_carro.estado_id, 
        {$wpdb->prefix}posts.post_title, 
        {$wpdb->prefix}posts.post_name,
        {$wpdb->prefix}itc_tienda_carro_detalle.cantidad,
        {$wpdb->prefix}itc_tienda_carro_detalle.producto_id,
        {$wpdb->prefix}itc_tienda_carro.direccion,
        {$wpdb->prefix}itc_tienda_carro.observaciones,
        {$wpdb->prefix}itc_tienda_carro.telefono,
        {$wpdb->prefix}itc_tienda_carro.ciudad,
        {$wpdb->prefix}itc_tienda_carro.tipo_pago,
        {$wpdb->prefix}users.user_email, 
        {$wpdb->prefix}users.display_name
    from 
        {$wpdb->prefix}itc_tienda_carro_detalle 
        inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id 
        inner join {$wpdb->prefix}itc_tienda_carro_estado on {$wpdb->prefix}itc_tienda_carro_estado.id={$wpdb->prefix}itc_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}itc_tienda_carro_detalle.producto_id  
        inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}itc_tienda_carro.usuario_id
    where {$wpdb->prefix}itc_tienda_carro.id ='".sanitize_text_field($_POST['id'])."';
    "); 
    ?>
    <!-- HTML que muestra los datos principales de la venta -->
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
                <!-- Mostramos cada dato de la venta -->
                <td><?php echo $datos[0]->display_name?></td>
                <td><?php echo  $datos[0]->telefono; ?></td>
                <td><?php echo $datos[0]->user_email?></td>
                <td><?php echo  $datos[0]->direccion; ?></td>
                <td><?php echo  $datos[0]->observaciones; ?></td>
                <td><?php $date = date_create($datos[0]->fecha);echo  date_format($date, 'd/m/Y'); ?></td>
                <td>$<?php echo number_format($datos[0]->monto, 0, '', '.');?></td>
                <td>
                    <?php
                    // Switch para mostrar el estado con diferentes estilos visuales
                    switch($datos[0]->estado_id){
                        case '1': ?>
                          <div class="badge bg-primary text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;

                        case '2': ?>
                          <div class="badge bg-secondary text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;

                        case '3': ?>
                          <div class="badge bg-warning text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;

                        case '4': ?>
                          <div class="badge bg-success text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;

                        case '5': ?>
                          <div class="badge bg-danger text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;

                        case '6': ?>
                          <div class="badge bg-primary text-wrap" style="width: 5rem;">
                          <?php echo $datos[0]->estado;?>
                          </div>
                        <?php break;
                    } ?>    
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
    $sum=0; // Variable para acumular el total
    foreach($datos as $dato){
        // Sumamos el monto de cada producto * cantidad
        $sum=$sum+get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad;
        ?>
        <tr> 
            <td style="text-align:center;">
              <!-- Mostramos la imagen del producto -->
              <a href="<?php echo get_site_url()."/".$dato->post_name?>" title="<?php echo $dato->post_title ?>" target="_blank">
                  <img class="img-fluid" style="width: 50px;height: 50px;" src="<?php echo  get_site_url().substr(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0],strlen(get_site_url()), strlen(wp_get_attachment_image_src( get_post_thumbnail_id($dato->producto_id), 'post')[0])); ?>" alt="<?php echo $dato->post_title ?>" />
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
    die(); // Terminamos el script
});
// Acción AJAX para editar una venta
add_action('after_setup_theme', function(){
    // Verifica si existe el nonce y si la acción recibida es "venta-edit"
    if(isset($_POST['nonce']) and $_POST['action']=='venta-edit'){
        global $wpdb; // Conexión global a la base de datos de WordPress
        
        // Actualiza el estado de la venta en la base de datos
        $wpdb->query("update {$wpdb->prefix}itc_tienda_carro 
                      set estado_id='".sanitize_text_field($_POST['estado_id'])."' 
                      where id='".sanitize_text_field($_POST['venta_id'])."' ");
        
        // Redirige a la misma página con un mensaje de confirmación
        wp_safe_redirect( $_POST['return']."&msg=1" ); 
        exit; // Detiene la ejecución
    }
});

// Acción AJAX que muestra el formulario de edición de ventas
add_action('wp_ajax_itc_tienda_ventas_editar_ajax', function(){
    global $wpdb; // Conexión a la BD
    
    // Obtiene los datos de la venta seleccionada
    $datos=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro 
                               where id='".sanitize_text_field($_POST['id'])."';");
    // Obtiene todos los estados disponibles
    $estados=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_estado");
    ?>
    <div class="row">
        <!-- Formulario de edición de ventas -->
        <form action="" method="POST" name="itc_tienda_form_ventas">
            <div class="mb-3">
                <label for="estado_id" class="form-label">Estado:</label>
                <!-- Select con los estados -->
                <select name="estado_id" id="estado_id" class="form-control">
                    <?php
                    foreach($estados as $estado){
                        ?>
                        <option value="<?php echo $estado->id;?>" 
                            <?php echo ($estado->id==$datos[0]->estado_id)?'selected="true"':'';?>>
                            <?php echo $estado->nombre;?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            
            <hr/>
            <!-- Datos ocultos necesarios para procesar el formulario -->
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('seg');?>" />
            <input type="hidden" name="action" value="venta-edit" />
            <input type="hidden" name="return" value="<?php echo (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>" />
            <input type="hidden" name="venta_id" value="<?php echo sanitize_text_field($_POST['id'])?>" />
            
            <!-- Botón para enviar -->
            <a href="javascript:void(0);" class="btn btn-primary" title="Editar" onclick="send_editar_ventas();">
                <i class="fas fa-edit"></i> Editar
            </a>
        </form>
    </div>
    <?php
    die(); // Corta la ejecución porque es una llamada AJAX
});

// Acción AJAX para generar filtros dinámicos
add_action('wp_ajax_itc_tienda_ventas_filtro_ajax', function(){
    global $wpdb; // Conexión a la BD
    
    // Según el filtro recibido en POST[id], genera el formulario correspondiente
    switch(sanitize_text_field( $_POST['id'] )){
        
        case 'estado':
            // Obtiene todos los estados
            $estados=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_estado");
            ?>
            <div class="row">
                <!-- Formulario de filtro por estado -->
                <form action="" method="GET" name="form_filtro_venta">
                    <div class="mb-3">
                        <label for="filtro_valor" class="form-label">Estado:</label>
                        <select name="filtro_valor" id="filtro_valor" class="form-control">
                            <?php foreach($estados as $estado){ ?>
                                <option value="<?php echo $estado->id;?>"><?php echo $estado->nombre;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <hr />
                    <!-- Datos ocultos -->
                    <input type="hidden" name="filtro" value="1" id="filtro" />
                    <input type="hidden" name="filtro_tipo" value="estado" />
                    <a href="javascript:void(0):" 
                       onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php');" 
                       class="btn btn-success" title="Buscar">
                       <i class="fas fa-search"></i> Buscar
                    </a>
                </form>
            </div>
            <?php
            die(); // Termina AJAX
        break;

        case 'pasarela':
            // Obtiene todas las pasarelas de pago
            $pasarelas=$wpdb->get_results("select * from {$wpdb->prefix}itc_tienda_carro_pasarelas");
            ?>
            <div class="row">
                <!-- Formulario de filtro por pasarela -->
                <form action="" method="GET" name="form_filtro_venta"> 
                    <div class="mb-3">
                        <label for="filtro_valor" class="form-label">Pasarela:</label>
                        <select name="filtro_valor" id="filtro_valor" class="form-control">
                            <?php foreach($pasarelas as $pasarela){ ?>
                                <option value="<?php echo $pasarela->id;?>"><?php echo $pasarela->nombre;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <hr />
                    <input type="hidden" name="filtro" value="1" id="filtro" />
                    <input type="hidden" name="filtro_tipo" value="pasarela" />
                    <a href="javascript:void(0):" 
                       onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php');" 
                       class="btn btn-success" title="Buscar">
                       <i class="fas fa-search"></i> Buscar
                    </a>
                </form>
            </div>
            <?php
            die();
        break;

        case 'cliente':
            // Obtiene los clientes que realizaron compras
            $clientes=$wpdb->get_results("select distinct({$wpdb->prefix}itc_tienda_carro.usuario_id) as id, 
                                                {$wpdb->prefix}users.display_name as nombre 
                                          from {$wpdb->prefix}itc_tienda_carro 
                                          inner join {$wpdb->prefix}users 
                                          on {$wpdb->prefix}users.ID={$wpdb->prefix}itc_tienda_carro.usuario_id;");
            ?>
            <div class="row">
                <!-- Formulario de filtro por cliente -->
                <form action="" method="GET" name="form_filtro_venta"> 
                    <div class="mb-3">
                        <label for="filtro_valor" class="form-label">Cliente:</label>
                        <select name="filtro_valor" id="filtro_valor" class="form-control">
                            <?php foreach($clientes as $cliente){ ?>
                                <option value="<?php echo $cliente->id;?>"><?php echo $cliente->nombre;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <hr />
                    <input type="hidden" name="filtro" value="1" id="filtro" />
                    <input type="hidden" name="filtro_tipo" value="cliente" />
                    <a href="javascript:void(0):" 
                       onclick="send_form_filtro_venta('<?php echo get_site_url();?>/wp-admin/admin.php?page=itc_tienda%2Fincludes%2Fadmin%2Fventas.php');" 
                       class="btn btn-success" title="Buscar">
                       <i class="fas fa-search"></i> Buscar
                    </a>
                </form>
            </div>
            <?php
            die();   
        break;
    }
});
// Creamos PDF al detectar parámetro "pdf" en la URL
add_action('after_setup_theme', function(){
    
    // Verificamos si existe el parámetro GET 'pdf' y que sea numérico
    if(isset($_GET['pdf']) and is_numeric($_GET['pdf'])){
        
        // Accedemos a la base de datos de WordPress
        global $wpdb;
 
        // Consulta SQL para obtener todos los datos de la orden de venta
        $datos=$wpdb->get_results("select 
        {$wpdb->prefix}itc_tienda_carro.id, 
        {$wpdb->prefix}itc_tienda_carro.fecha, 
        {$wpdb->prefix}itc_tienda_carro.monto,
        {$wpdb->prefix}itc_tienda_carro_estado.nombre as estado, 
        
        {$wpdb->prefix}posts.post_title, 
        {$wpdb->prefix}posts.post_name,
        {$wpdb->prefix}itc_tienda_carro_detalle.cantidad,
        {$wpdb->prefix}itc_tienda_carro_detalle.producto_id,
        {$wpdb->prefix}itc_tienda_carro.direccion,
        {$wpdb->prefix}itc_tienda_carro.observaciones,
        {$wpdb->prefix}itc_tienda_carro.telefono,
        {$wpdb->prefix}itc_tienda_carro.ciudad,
        {$wpdb->prefix}itc_tienda_carro.tipo_pago,
        {$wpdb->prefix}users.user_email, 
        {$wpdb->prefix}users.display_name
        from 
        {$wpdb->prefix}itc_tienda_carro_detalle 
        inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id 
        inner join {$wpdb->prefix}itc_tienda_carro_estado on {$wpdb->prefix}itc_tienda_carro_estado.id={$wpdb->prefix}itc_tienda_carro.estado_id 
        inner join {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID={$wpdb->prefix}itc_tienda_carro_detalle.producto_id  
        inner join {$wpdb->prefix}users on {$wpdb->prefix}users.ID={$wpdb->prefix}itc_tienda_carro.usuario_id
        where {$wpdb->prefix}itc_tienda_carro.id ='".sanitize_text_field($_GET['pdf'])."';
                "); 
        
        // Incluimos Dompdf (librería para generar PDFs)
        require 'vendor/autoload.php';
        
        // Instanciamos Dompdf con la opción de permitir imágenes remotas
        $dompdf = new Dompdf(array('enable_remote' => true));
        
        // Comenzamos a armar el HTML del PDF
        $html='';
        $html.='<h1>Orden de venta N° '.sanitize_text_field($_GET['pdf']).'</h1>';
        
        // Tabla de datos principales de la orden
        $html.='<table border=1>    
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
        <tbody>';
        
        // Convertimos la fecha de la base a formato legible
        $date = date_create($datos[0]->fecha);
        
        // Cargamos los datos principales en una fila de la tabla
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
        
        // Sección detalle de productos
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
        
        // Variable para acumular el monto total de los productos
        $sum=0;
        
        // Recorremos los productos de la orden
        foreach($datos as $dato){
            $sum=$sum+get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad;
            
            // Agregamos cada producto como fila de la tabla
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
        
        // Pasamos el HTML a Dompdf
        $dompdf->loadHtml($html); 
        
        // Configuramos el tamaño y orientación de la hoja
        $dompdf->setPaper('A4', 'landscape'); 
        
        // Renderizamos el PDF
        $dompdf->render();  
        
        // Mostramos el PDF en pantalla con nombre único basado en timestamp
        return $dompdf->stream(time().'.pdf');
    }
});

