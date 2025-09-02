<?php 
if(!defined('ABSPATH')) die();
$categorias=get_categories(array(
    'taxonomy'   => 'category',
    'orderby'    => 'name',
));
?>
<div class="row">
    <div class="col-12">
        <form action="<?php echo get_site_url();?>" method="GET" name="search">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar....." name="s" id="s" value="<?php echo isset($_GET['s']) ? sanitize_text_field($_GET['s']) : ''; ?>" />
                <button class="btn btn-outline-secondary" type="button" id="button-addon2" title="Buscar" onclick="buscador();"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </form>   
    </div>
    <hr />
    <div class="col-12">
        <h1 class="h2 pb-4 sidebar-title">Categorías</h1>
        <ul class="list-unstyled templatemo-accordion">
            <?php
            foreach($categorias as $categoria){
            ?>    
            <li class="pb-3">
                <a class="d-flex justify-content-between h3 text-decoration-none" href="<?php echo get_category_link($categoria->term_id)?>" title="<?php echo $categoria->name?>">
                    <?php echo $categoria->name?>
                </a>
            </li>
            <?php }?>
        </ul>
    </div>
</div>

<style>
/* ================= Sidebar: Títulos y Categorías ================= */

/* Títulos de buscar y categorías */
.sidebar-title {
    font-weight: 600;             /* más grueso */
    text-decoration: underline;   /* subrayado */
    color: #ffd900ff;             /* amarillo */
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

/* Lista de categorías */
.templatemo-accordion {
    list-style: none;             /* quitar bullets */
    padding-left: 0;
    margin: 0;
}

.templatemo-accordion li a {
    font-size: 0.9rem;            /* más pequeña que el título */
    font-weight: 500;              /* un poco más firme */
    color: #000;
    text-decoration: none;
    transition: color 0.3s;
}

.templatemo-accordion li a:hover {
    color: #ffd900ff;             /* amarillo al pasar mouse */
}
</style>
