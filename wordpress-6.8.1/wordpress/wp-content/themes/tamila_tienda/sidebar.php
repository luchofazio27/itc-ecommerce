<?php 
if(!defined('ABSPATH')) die();
$categorias=get_categories(array(
    'taxonomy'   => 'category',
    'orderby'    => 'name',
));
?>
<div class="row">
    <div class="col-12">
    <h1 class="h2 pb-4">Buscar</h1>
    <form action="<?php echo get_site_url();?>" method="GET" name="search">
     <div class="input-group mb-3">
    <input type="text" class="form-control" placeholder="Buscar....." name="s" id="s" value="<?php echo sanitize_text_field($_GET['s']);?>" />
    <button class="btn btn-outline-secondary" type="button" id="button-addon2" title="Buscar" onclick=buscador();><i class="fas fa-search"></i> Buscar</button>
    </div>
     </form>   
    </div>
    <hr />
    <div class="col-12">
    <h1 class="h2 pb-4">Categorías</h1>
    <ul class="list-unstyled templatemo-accordion">
        <?php
        foreach($categorias as $categoria){
        ?>    
        <li class="pb-3">
            <a class="  d-flex justify-content-between h3 text-decoration-none  " href="<?php echo get_category_link( $categoria->term_id )?>" title="<?php echo $categoria->name?>">
                <?php echo $categoria->name?></a>
                        
            </li>
            <?php }?>
        </ul>
    </div>
</div>