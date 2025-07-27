<?php
if(!defined('ABSPATH')) die();
?>
  <footer class="bg-dark" id="tempaltemo_footer">
        <div class="container">
            

            <div class="row text-light mb-4">
                <div class="col-12 mb-3">
                    <div class="w-100 my-3 border-top border-light"></div>
                </div>
                <div class="col-auto me-auto">
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="http://facebook.com/"><i class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.instagram.com/"><i class="fab fa-instagram fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://twitter.com/"><i class="fab fa-twitter fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.linkedin.com/"><i class="fab fa-linkedin fa-lg fa-fw"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="subscribeEmail">Email address</label>
                    <div class="input-group mb-2">
                       <img src="<?php echo get_site_url()."/wp-content/themes/tamila_tienda/assets/img/4pasarelacontorno.png"?>" alt="" height="100" class="img-fluid" />
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 bg-black py-3">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="text-left text-light">
                            &copy; Todos los derechos reservados 
                            | Desarrollado por <a rel="sponsored" href="https://www.cesarcancino.com" target="_blank" title="César Cancino">César Cancino</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>
    <!-- End Footer -->
<!--footer wordpress-->
<?php wp_footer();?>
<!--//footer wordpress-->
    <!-- Start Script -->
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/jquery-1.11.0.min.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/templatemo.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/sweetalert2.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/custom.js"></script>
    <!-- End Script -->
</body>

</html>