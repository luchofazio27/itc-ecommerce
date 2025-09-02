<?php 
if(!defined('ABSPATH')) die();
get_header();
global $paged;
$curpage = $paged ? $paged : 1;
$query=new WP_Query(array(
    'post_type'=>'itc_productos',
    'posts_per_page'=>6,
    'orderby' => 'id',
    'order'=>'DESC',
    'paged' => $paged
));
?>
<div class="container py-5">
    <div class="row">
<!-- Sidebar -->
<div class="col-lg-3 mb-4">
    <aside class="sidebar-box p-3 shadow-sm rounded bg-lightgray">
        <?php get_sidebar();?>
    </aside>
</div>


        
        <!-- Contenido principal -->
        <!-- Cambiar clase de la columna principal para fondo gris -->
<div class="col-lg-9 bg-lightgray p-3 rounded">

            <!-- Migas de pan -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light p-2 rounded shadow-sm">
                    <li class="breadcrumb-item"><a href="<?php echo get_site_url();?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuestros productos</li>
                </ol>
            </nav>

            <h2 class="mb-4 text-dark fw-bold border-bottom pb-2">Nuestros productos</h2>

            <!-- Grid de productos -->
            <div class="row g-4">
                <?php 
                while($query->have_posts()){
                    $query->the_post();
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            <!-- Imagen -->
                            <!-- Imagen -->
<div class="position-relative product-img-container">
    <?php the_post_thumbnail('medium', array('class'=>'card-img-top rounded-top img-fluid product-img'));?>
    <div class="card-img-overlay d-flex justify-content-center align-items-center p-0 overlay-fade">
        <a class="btn btn-primary rounded-circle shadow" href="<?php the_permalink();?>" title="<?php the_title();?>">
            <i class="far fa-eye"></i>
        </a>
    </div>
</div>


                            <!-- Info producto -->
                            <div class="card-body text-center">
                                <h5 class="card-title mb-2">
                                    <a href="<?php the_permalink();?>" class="text-decoration-none text-dark fw-bold" title="<?php the_title();?>">
                                        <?php the_title();?>
                                    </a>
                                </h5>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning text-dark">
    Stock: <?php echo get_post_meta(get_the_ID(), 'Stock')[0];?>
</span>
<span class="fw-bold text-dark h5 mb-0">
    $<?php echo number_format(get_post_meta(get_the_ID(), 'precio')[0], 0, '','.');?>
</span>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Paginación -->
            <div class="row mt-5">
                <div class="col">
                    <?php 
                    echo '
                    <ul class="pagination justify-content-center">
                        <li class="page-item"> <a class="page-link" href="'.get_pagenum_link(1).'">&laquo;</a></li>
                        <li class="page-item"><a class="page-link" href="'.get_pagenum_link(($curpage-1 > 0 ? $curpage-1 : 1)).'">&lsaquo;</a></li>';
                        for($i=1;$i<=$query->max_num_pages;$i++){
                            echo ' 
                            <li class="page-item '.($i == $curpage ? 'active' : '').'">
                                <a class="page-link" href="'.get_pagenum_link($i).'">'.$i.'</a>
                            </li>';
                        }
                    echo '
                        <li class="page-item"><a class="page-link" href="'.get_pagenum_link(($curpage+1 <= $query->max_num_pages ? $curpage+1 : $query->max_num_pages)).'">&rsaquo;</a></li>
                        <li class="page-item"><a class="page-link" href="'.get_pagenum_link($query->max_num_pages).'">&raquo;</a></li>
                    </ul>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos personalizados */
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.overlay-fade {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    background: rgba(0,0,0,0.3);
}
.product-card:hover .overlay-fade {
    opacity: 1;
}
.overlay-fade a {
    font-size: 1.2rem;
    padding: 12px;
    background-color: #ffd900ff; /* Amarillo legible para el botón */
    color: #000 !important;      /* Texto negro sobre amarillo */
}

/* 🔹 Ajuste uniforme de imágenes en cards */
.product-img-container {
    width: 100%;
    height: 220px; /* altura fija para todas las miniaturas */
    overflow: hidden;
}
.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.5rem 0.5rem 0 0;
}

/* Precios negros */
.product-card .fw-bold {
    color: #000 !important;
}

/* ===== Sidebar ===== */
.sidebar-box {
    border-left: 4px solid #ffd900ff; /* línea decorativa amarilla */
    background-color: #f8f9fa;        /* sidebar gris claro */
}

/* Ocultar títulos genéricos de widgets */
.sidebar-box h2.widgettitle,
.sidebar-box h3.widgettitle {
    display: none;
}

/* Fondo blanco para la tienda */
.bg-lightgray {
    background-color: #fff !important;
}

/* Un poco más de aire entre sidebar y contenido */
@media (min-width: 992px) {
    .col-lg-3 {
        border-right: 1px solid #e0e0e0;
    }
}
</style>


<?php
get_footer();
