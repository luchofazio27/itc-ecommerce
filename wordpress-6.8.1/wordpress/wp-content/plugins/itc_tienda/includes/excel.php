<?php 
// Evita el acceso directo al archivo si no está dentro de WordPress
if(!defined('ABSPATH')) die();

// Importamos las clases necesarias de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Verificamos si no existe la función (para no redeclararla en caso de carga múltiple)
if(!function_exists('itc_tienda_ventas_excel')){
    
    // Definimos la función que genera el Excel
    function itc_tienda_ventas_excel(){
        
        // Validamos si en la URL viene el parámetro "excel"
        if(isset($_GET['excel']))
        {
            // Accedemos al objeto de base de datos global de WordPress
            global $wpdb;

            // Ejecutamos consulta SQL para obtener los datos de las ventas
            $datos=$wpdb->get_results("select 
            {$wpdb->prefix}itc_tienda_carro.id, 
            {$wpdb->prefix}itc_tienda_carro.fecha, 
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
            order by {$wpdb->prefix}itc_tienda_carro.id desc;
            "); 

            // Cargamos las dependencias de Composer
            require 'vendor/autoload.php';

            // Creamos un helper de PhpSpreadsheet (permite validar si está en CLI o navegador)
            $helper = new Sample();
            if ($helper->isCli()) {
                $helper->log('Este ejemplo solo debe ejecutarse desde un navegador web' . PHP_EOL);
                return; // Si es CLI, cortamos la ejecución
            }

            // Creamos un nuevo objeto de hoja de cálculo
            $spreadsheet = new Spreadsheet();

            // Definimos las propiedades del archivo Excel
            $spreadsheet->getProperties()
                        ->setCreator('ITC') // Cambié "Tamila" por "ITC"
                        ->setLastModifiedBy('ITC.cl')
                        ->setTitle('Office 2007 XLSX Test Document')
                        ->setSubject('Office 2007 XLSX Test Document')
                        ->setDescription('Excel creado con PHP.')
                        ->setKeywords('office 2007 openxml php')
                        ->setCategory('Test result file');

            // Definimos los encabezados de las columnas
            $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'N°')
                        ->setCellValue('B1', 'Producto')
                        ->setCellValue('C1', 'Cantidad')
                        ->setCellValue('D1', 'Cliente')
                        ->setCellValue('E1', 'Teléfono')
                        ->setCellValue('F1', 'E-Mail')
                        ->setCellValue('G1', 'Dirección')
                        ->setCellValue('H1', 'Observaciones')
                        ->setCellValue('I1', 'Fecha')
                        ->setCellValue('J1', 'Monto')
                        ->setCellValue('K1', 'Estado');
 
            // Empezamos a llenar los datos desde la fila 2
            $i=2;

            // Recorremos cada registro obtenido de la BD
            foreach($datos as $dato) 
            {
                // Formateamos la fecha
                $date = date_create($dato->fecha);

                // Insertamos cada dato en una celda correspondiente
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $dato->id)
                ->setCellValue('B'.$i, $dato->post_title)
                ->setCellValue('C'.$i, $dato->cantidad )
                ->setCellValue('D'.$i, $dato->display_name)
                ->setCellValue('E'.$i, $dato->telefono)
                ->setCellValue('F'.$i, $dato->user_email)
                ->setCellValue('G'.$i, $dato->direccion)
                ->setCellValue('H'.$i, $dato->observaciones)
                ->setCellValue('I'.$i, date_format($date, 'd/m/Y'))
                ->setCellValue('J'.$i, number_format(get_post_meta( $dato->producto_id, 'precio' )[0]*$dato->cantidad, 0, '', '.'))
                ->setCellValue('K'.$i, $dato->estado);
                $i++; // Pasamos a la siguiente fila
            }

            // Ponemos un nombre a la hoja de cálculo
            $spreadsheet->getActiveSheet()->setTitle('Hoja 1');

            // Definimos como activa la primera hoja
            $spreadsheet->setActiveSheetIndex(0);

            // Encabezados HTTP para forzar la descarga del archivo Excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte_'.time().'.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            // Generamos el archivo Excel y lo enviamos al navegador
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit; // Finalizamos ejecución
        }
    }
    // Registramos la función para que se ejecute después de que se cargue el tema de WordPress
    add_action( 'after_setup_theme', 'itc_tienda_ventas_excel' );
}
