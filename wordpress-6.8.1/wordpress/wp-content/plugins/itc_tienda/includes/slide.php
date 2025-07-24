<?php
if (!defined('ABSPATH')) die();
if (!function_exists('itc_tienda_slide_init')) {
    function itc_tienda_slide_init()
    {
        global $wpdb;
        $sql = "create table if not exists 
            {$wpdb->prefix}itc_tienda_slide
            (
            id int not null auto_increment,
            nombre varchar(100) not null,
            foto_id int,
            primary key (id)
            ); 
            ";
        $wpdb->query($sql);
        $index = "alter table {$wpdb->prefix}itc_tienda_slide add index(`nombre`);";
        $wpdb->query($index);
        $index2 = "alter table {$wpdb->prefix}itc_tienda_slide add index(`foto_id`);";
        $wpdb->query($index2);
    }
}
if (!function_exists('itc_tienda_slide_desactivar')) {
    function itc_tienda_slide_desactivar()
    {
        global $wpdb;
        $sql = "drop table {$wpdb->prefix}itc_tienda_slide";
        $wpdb->query($sql);
    }
}
//shortcode
//[itc_tienda_slide id="10"]Liff Domotic[/itc_tienda_slide]
if (!function_exists('itc_tienda_slide_codigo_corto')) {
    add_action('init', 'itc_tienda_slide_codigo_corto');
    function itc_tienda_slide_codigo_corto()
    {
        add_shortcode('itc_tienda_slide', 'itc_tienda_slide_codigo_corto_display');
    }
}
if (!function_exists('itc_tienda_slide_codigo_corto_display')) {
    function itc_tienda_slide_codigo_corto_display($argumentos, $content = "")
    {


        global $wpdb;

        $query = "select * from {$wpdb->prefix}itc_tienda_slide order by id desc;";
        $datos = $wpdb->get_results($query, ARRAY_A);

?>
        <!--tamila_galeria-->
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                foreach ($datos as $key => $dato) {
                ?>
                    <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?>">
                        <img
                            src="<?php echo get_site_url() . $dato['nombre']; ?>"
                            class="d-block w-100 img-fluid"
                            style="height: 450px; object-fit: cover; object-position: center;" />
                    </div>
                <?php
                }
                ?>

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <!--//tamila_galeria-->
<?php
    }
}
