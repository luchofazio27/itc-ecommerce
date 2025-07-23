<?php 
if(!defined('ABSPATH')) die();
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//composer require phpoffice/phpspreadsheet
if(!function_exists('tamila_tienda_ventas_excel')){
    function tamila_tienda_ventas_excel(){
        if(isset($_GET['excel']))
        {
           
            global $wpdb;
 
            $datos=$wpdb->get_results("select 
            {$wpdb->prefix}tamila_tienda_carro.id, 
            {$wpdb->prefix}tamila_tienda_carro.fecha, 
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
            order by {$wpdb->prefix}tamila_tienda_carro.id desc;
                    "); 
        require 'vendor/autoload.php';


$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('Este ejemplo solo debe ejecutarse desde un navegador web' . PHP_EOL);

    return;
}
$spreadsheet = new Spreadsheet();

$spreadsheet->getProperties()
            ->setCreator('Tamila')
            ->setLastModifiedBy('Tamila.cl')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Excel creado con PHP.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

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
 
$i=2;
foreach($datos as $dato) 
{
    $date = date_create($dato->fecha);
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
    $i++;
}

$spreadsheet->getActiveSheet()->setTitle('Hoja 1');

$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_'.time().'.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
    }
}
    add_action( 'after_setup_theme', 'tamila_tienda_ventas_excel' );
}
