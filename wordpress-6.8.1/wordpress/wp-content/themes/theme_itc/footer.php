<?php
if (!defined('ABSPATH')) die();
?>
<footer class="bg-dark p-2" id="tempaltemo_footer">
<style>
html,
body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Ajusta espacio entre íconos y texto */
.footer-icons {
    margin-bottom: 0.5rem !important;
}

.footer-icons li {
    margin-right: 0.4rem;
}

/* Contenedor de la fila con la imagen de pago */
.footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Imagen medios de pago más chica y centrada verticalmente */
.footer-pay {
    height: 100px;            
    object-fit: contain;     
    margin-left: 0.5rem;
    margin-right: 0.5rem;
    filter: drop-shadow(0 0 1px rgba(255,255,255,1)) drop-shadow(0 0 1px rgba(255,255,255,0.8));

}

.footer-text {
    font-size: 0.8rem !important;
}

/* ======= Responsivo ======= */
@media (max-width: 767px) {
    .footer-row {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .footer-row > .col-auto {
        width: 100%;
        text-align: center;
        margin: 0 auto;
    }

    .footer-icons {
        margin-bottom: 1rem !important;
        display: inline-flex;
        justify-content: center;
        flex-wrap: wrap;
        padding-left: 0;
    }

    .footer-pay {
        display: block;
        margin: 0.5rem auto;   
    }

    .footer-text {
        text-align: center;
    }
}


</style>


    <div class="container">

        <!-- ICONOS + IMAGEN DE PAGO -->
        <div class="row text-light mb-2 footer-row">
            <div class="col-auto me-auto">
                <ul class="list-inline text-left footer-icons mb-0">
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
            <div class="col-auto d-flex align-items-center">
                <img src="<?php echo get_site_url() . "/wp-content/themes/theme_ITC/assets/images/pago-seguro.png" ?>" 
                     alt="Medios de Pago" class="footer-pay" />
            </div>
        </div>

        <!-- TEXTO -->
        <div class="row text-light py-2">
            <div class="col-12">
                <p class="mb-0 footer-text">
                    Juan A. Garcia 1816 Paternal - Buenos Aires - CP 1416 - Republica Argentina<br>
                    Tel: 54 11 4588 0015 / Fax: 55 11 4584 9101<br>
                    Todos los derechos reservados | Desarrollado por 
                    <a rel="sponsored" href="https://www.instagram.com/liffdomotic?igsh=MWo0ajdjbXVkNzZvdw==" target="_blank" title="Liff Domotic">LiffDomotic</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!--footer wordpress-->
<?php wp_footer(); ?>
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
