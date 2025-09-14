<?php 
if(!defined('ABSPATH')) die();
get_header();
?>

<div class="container py-5">
  <div class="row justify-content-center text-center py-5">
    <div class="col-md-8">
      <h1 class="display-4 fw-bold text-white mb-3">Ups... Página no encontrada</h1>
      <h3 class="text-warning mb-4">Error 404</h3>
      <p class="text-white-50 mb-4">
        La página que buscás no existe o fue movida.  
        Podés volver al inicio para seguir navegando.
      </p>
      <a href="<?php echo home_url(); ?>" class="btn btn-warning btn-lg rounded-pill px-4">
        Volver al inicio
      </a>
    </div>
  </div>
</div>

<?php 
get_footer();
?>
