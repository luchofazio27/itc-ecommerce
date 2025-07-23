<?php
if(!defined('ABSPATH')) die();
global $wpdb;
$tabla = "{$wpdb->prefix}tamila_tienda_productos_galeria"; 

$query=new WP_Query( [
    'post_type'=>'tamila_productos',
    'orderby' => 'id',
    'order'=>'DESC'
] );

?>
<div class="wrap">
<h1 class="wp-heading-inline"><?php echo get_admin_page_title()?></h1>
<hr />
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>URL</th>
                    <th>Fotos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($query->have_posts()){
                    $query->the_post();
                    ?>
                <tr>
                    <td><?php the_ID();?></td>
                    <td><?php the_title();?></td>
                    <td style="text-align:center;">
                        <a href="<?php echo get_site_url()."/".get_post_field( 'post_name', get_post() );?>" title="<?php the_title();?>" target="_blank"><i class="fas fa-link"></i></a>
                    </td>
                    <td style="text-align:center;">
                        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=tamila_tienda%2Fincludes%2Fadmin%2Feditar.php&id=<?php echo the_ID();?>&nombre=<?php the_title();?>" title="Fotos"><i class="fas fa-images"></i></a>
                    </td>
                </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>