<?php 
if(!defined('ABSPATH')) die();
get_header();
$categoria=get_queried_object();
global $paged;
$curpage = $paged ? $paged : 1;
$query=new WP_Query(array(
    'post_type'=>'itc_productos',
    'posts_per_page'=>6,
    'orderby' => 'id',
    'order'=>'DESC',
    'paged' => $paged,
    'category_name'=>$categoria->slug //Solamente productos de la categoría actual
));
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <?php get_sidebar();?>
        </div>
        <div class="col-lg-9">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a  href="<?php echo get_site_url();?>">Home </a></li>
                <li class="breadcrumb-item"><a href="<?php echo get_site_url();?>/tienda">Tienda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categoría: <strong><?php echo $categoria->name ;?></strong</li>
            </ol>
        </nav>
        <hr/>
        <!--row-->
        <div class="row">
            <div class="col-md-6">
                <ul class="list-inline shop-top-menu pb-3 pt-1">
                    <li class="list-inline-item">
                        <a class="h3 text-dark text-decoration-none mr-3" >Nuestros productos</a>
                    </li>
                            
                </ul>
            </div>
            <div class="col-md-6 pb-4">
                         
            </div>
        </div>
        <!--/row-->
        <!--row-->
        <div class="row">
            <?php 
            while($query->have_posts()){
                $query->the_post();
                ?>
                  <!--ítem-->
                  <div class="col-md-4">
                    <div class="card mb-4 product-wap rounded-0">
                        <div class="card rounded-0">
                            <?php the_post_thumbnail(' ', array('class'=>'card-img rounded-0 img-fluid'));?>
                            <div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                <ul class="list-unstyled">
                                    <li><a class="btn btn-success text-white mt-2" href="<?php the_permalink();?>" title="<?php the_title();?>"><i class="far fa-eye"></i></a></li>   
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <a href="<?php the_permalink(  );?>" class="h3 text-decoration-none" title="<?php the_title();?>"><?php the_title();?></a>
                            <hr/>
                            <div class="row">
                                    <div class="col-4">
                                    <span class="text-center mb-0">Stock: <?php echo get_post_meta( get_the_ID(), 'Stock' )[0]?></span>
                                    </div>
                                    <div class="col-2">|</div>
                                    <div class="col-6">
                                    <span class="text-center mb-0">$<?php echo number_format(get_post_meta( get_the_ID(), 'precio' )[0], 0, '','.');?></span>
                                    </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <!--/ítem-->  
                <?php

            }
            ?>
        </div>
        <!--/row-->
        <!--row-->
        <div class="row">
        <?php 
                    echo '
         <ul class="pagination pagination-lg justify-content-end">
         <li class="page-item"> <a class="page-link" href="'.get_pagenum_link(1).'">&laquo;</a></li>
         <li class="page-item"><a class="page-link rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="'.get_pagenum_link(($curpage-1 > 0 ? $curpage-1 : 1)).'">&lsaquo;</a></li>';
             for($i=1;$i<=$query->max_num_pages;$i++){
                 echo ' <li class="page-item rounded-0 mr-3 shadow-sm border-top-0 border-left-0"><a class="page-link rounded-0 mr-3 shadow-sm border-top-0 border-left-0 '.($i == $curpage ? 'active ' : '').'" href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
             }
        echo '
             <li class="page-item"><a class="page-link rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="'.get_pagenum_link(($curpage+1 <= $query->max_num_pages ? $curpage+1 : $query->max_num_pages)).'">&rsaquo;</a></li>
             <li class="page-item"><a class="page-link rounded-0 mr-3 shadow-sm border-top-0 border-left-0" href="'.get_pagenum_link($query->max_num_pages).'">&raquo;</a></li>
         </ul> 
         ';
                    ?>
        </div>
        <!--/row-->
        
        </div>
    </div>
</div>
<?php
get_footer();