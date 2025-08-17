<?php
if (!defined("ABSPATH")) {
    die();
}
$query=new WP_Query( [
    'post_type'=>'itc_productos',
    'p'=>$post->ID//$post es una variable global de wordpress
]);
global $wpdb;
$fotos = $wpdb->get_results(
    "select * from {$wpdb->prefix}itc_tienda_productos_galeria where post_id='" .
        $post->ID .
        "' order by id desc;",
    ARRAY_A
); 
$carro = $wpdb->get_results( //obtenemos el carro del usuario
    "
    select 
    {$wpdb->prefix}itc_tienda_carro_detalle.id
    from 
    {$wpdb->prefix}itc_tienda_carro_detalle
    inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id 
    where
    {$wpdb->prefix}itc_tienda_carro.estado_id in(1,6)
    and 
    {$wpdb->prefix}itc_tienda_carro_detalle.producto_id='" .
        $post->ID .
        "';
"
);
 
get_header();
?>
<section class="bg-light">
    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-5 mt-5">
                <div class="card mb-3">
                <img class="card-img img-fluid" src="<?php echo get_the_post_thumbnail_url(
                           get_the_ID(),
                           "full"
                       ); ?>" alt="<?php the_title();?>" id="product-detail" /> 
                </div>
                <div class="row">
                    <!--Start Controls-->
                    <div class="col-1 align-self-center">
                        <a href="#multi-item-example" role="button" data-bs-slide="prev">
                        <i class="text-dark fas fa-chevron-left"></i>
                                <span class="sr-only">Previous</span>
                        </a>
                    </div>
                    <!--End Controls-->
                    <div id="multi-item-example" class="col-10 carousel slide carousel-multi-item" data-bs-ride="carousel">
                        <div class="carousel-inner product-links-wap" role="listbox">
                            <!--carrusel-->
                            <div class="carousel-item active">
                                <div class="row">
                                    <?php 
                                    $i=1;
                                    foreach($fotos as $foto){
                                        ?>
                                    <!--foto-->
                                    <div class="col-4">
                                        <a href="javascript:void(0);">
                                            <img class="card-img img-fluid" src="<?php echo get_site_url() .$foto["nombre"]; ?>" title="<?php echo $i; ?>" />
                                        </a>
                                    </div>
                                    <!--//foto-->
                                    <?php
                                            $i++;
                                            if (
                                                $i % 3 == 0 and
                                                $i < sizeof($fotos)
                                            ) { ?>
                                            </div>
                                            </div>  
                                             <!--//carrusel-->
                                             <!--carrusel--> 
                                            <div class="carousel-item  ">
                                        <div class="row">
                                                <?php }
                                            }
                                        ?>
                                        

                                </div>
                            </div>
                            <!--/carrusel-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 mt-5">
                <div class="card">
                    <div class="card-body">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a  href="<?php echo get_site_url(); ?>">Home </a></li>
                            <li class="breadcrumb-item"><a href="<?php echo get_site_url(); ?>/tienda">Tienda</a></li>
                            <li class="breadcrumb-item"><?php echo the_category(
                                " "
                            ); ?></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?> </li>
                        </ol>
                        </nav>
                        <hr/>
                        <h1 class="h2"><?php the_title(); ?></h1>
                        <p class="h3 py-2">Stock: <?php echo get_post_meta(
                                get_the_ID(),
                                "Stock"
                            )[0]; ?> | $<?php echo number_format(
                                get_post_meta(get_the_ID(), "precio")[0],
                                0,
                                "",
                                "."
                            ); ?></p>
                        <ul class="list-inline">
                                <li class="list-inline-item">
                                    <h6 class="categoria-single">Categoría: <?php echo the_category(
                                        " "
                                    ); ?></h6>
                                </li>
                               
                        </ul>
                        <h6>Descripción:</h6>
                        <p><?php the_content(); ?> </p>
                        <?php 
                        if(get_post_meta(get_the_ID(), "Stock")[0]>=1){
                            ?>
                        <form action="" method="GET" name="itc_tienda_form_single">
                        <!--cantidad-->
                        <hr />
                        <div class="row pb-3">
                        <input type="hidden" name="stock" id="stock" value="<?php echo get_post_meta(get_the_ID(),"Stock")[0]; ?>" />
                        <div class="col-auto">
                            <ul>
                                <li class="list-inline-item text-right">
                                    Cantidad (<?php echo get_post_meta(get_the_ID(),"Stock")[0]; ?> en Stock)
                                    <input type="hidden" name="product_quanity" id="product-quanity" value="1" />
                                </li>
                                <li class="list-inline-item">
                                    <span class="btn btn-success" id="btn-minus">-</span>
                                </li>
                                <li class="list-inline-item">
                                    <span class="badge bg-secondary" id="var-value">1</span>
                                </li>
                                <li class="list-inline-item">
                                    <span class="btn btn-success" id="btn-plus">+</span>
                                </li>
                            </ul>
                        </div>

                        </div>     
                        <!--/cantidad-->
                        <!--botones comprar-->
                        <div class="row pb-3">
                        <?php 
                        if(is_user_logged_in()){ // verificamos si el usuario está logueado
                            ?>
                        <div class="col d-grid">
                            <?php
                            if(sizeof($carro)==0){ // verificamos si el carro está vacío
                                ?>
                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-lg" onclick="itc_tienda_comprar('<?php the_ID();?>' , '<?php echo admin_url("admin-ajax.php");?>', '<?php echo wp_create_nonce("seg"); ?>', '<?php echo get_site_url();?>/checkout', '1');" title="Comprar">
                                <i class="fas fa-arrow-up"></i> Comprar
                            </a>
                                <?php
                            }
                            ?>
                           
                        </div>
                        <div class="col d-grid">
                        <?php 
                        if(sizeof($carro)==0){ // verificamos si el carro está vacío
                            ?>
                            <a href="javascript:void(0);" class="btn btn-outline-success btn-lg" onclick="itc_tienda_comprar('<?php the_ID();?>' , '<?php echo admin_url("admin-ajax.php");?>', '<?php echo wp_create_nonce("seg"); ?>', '<?php echo get_permalink();?>', '1');" title="Añadir al carrito">
                            <i class="fa fa-fw fa-cart-arrow-down"></i> Añadir al carrito
                            </a>
                            <?php
                        }else{ // si el carro no está vacío, mostramos el botón de quitar del carrito
                            ?>
                            <a href="javascript:void(0);" class="btn btn-outline-warning btn-lg" onclick="itc_tienda_comprar('<?php the_ID();?>' , '<?php echo admin_url("admin-ajax.php");?>', '<?php echo wp_create_nonce("seg"); ?>', '<?php echo get_permalink();?>', '2');" title="Quitar del carrito">
                            <i class="fa fa-fw fa-cart-arrow-down"></i> Quitar del carrito
                            </a>
                            <?php
                        }
                        ?>
                        <input type="hidden" name="carro_detalle_id" value="<?php echo !empty($carro) ? $carro[0]->id : ''; ?>" />
                            
                        </div>
                            <?php
                        }else{

                        }
                        ?>
                        </div> 
                        <!--/botones comprar-->
                        </form>
                            
                            <?php
                        }else{
                            ?>
                        <div class="alert alert-danger" role="alert">
                        <strong>Ups!!</strong> No tenemos stock disponible en estos momentos.
                        </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>