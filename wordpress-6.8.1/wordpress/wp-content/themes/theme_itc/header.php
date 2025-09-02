<?php
if (!defined('ABSPATH')) die();
$userdata = wp_get_current_user();
global $wpdb;
$carro=$wpdb->get_results("select count(*) as cuantos from {$wpdb->prefix}itc_tienda_carro_detalle 
inner join {$wpdb->prefix}itc_tienda_carro on {$wpdb->prefix}itc_tienda_carro.id={$wpdb->prefix}itc_tienda_carro_detalle.itc_tienda_carro_id
where 
{$wpdb->prefix}itc_tienda_carro.usuario_id='".$userdata->ID."'
and 
{$wpdb->prefix}itc_tienda_carro.estado_id in (1, 6);");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <title><?php bloginfo('name'); ?> <?php (!empty(wp_title())) ? '- ' . wp_title() : '' ?></title>
    <meta name="author" content="Web Master liffdomotic | liffdomotic@gmail.com" />
    <meta name="keywords" content="tienda" />
    <meta name="description" content="<?php bloginfo('description'); ?>" />
    <link rel="icon" href="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/templatemo.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/custom.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/sweetalert2.css" />
    <?php wp_head(); ?>


    
<style>
/* HEADER NEGRO SÓLIDO CON SOMBRA REAL */
nav.custom-header.navbar {
    background-color: #000000 !important; /* negro puro */
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
    height: 95px !important;
    z-index: 1000;

    /* sombra que cae sobre el contenido */
    box-shadow: 0 8px 20px rgba(0,0,0,0.8); /* eje vertical, desenfoque y transparencia */
}

/* Links de navegación - blanco con efecto de brillo */
.custom-header .nav-link {
    color: #ffffff !important;
    font-weight: 500;
    transition: all 0.4s ease;
    text-shadow: 0 0 0px #fff;
}

.custom-header .nav-link:hover {
    color: #ffffff !important;
    text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px #ffffff;
}

/* Iconos del carrito, login y demás a la derecha */
.custom-header .nav-icon i {
    color: #ffffff !important;
    font-size: 1.3rem;
    transition: all 0.4s ease;
    text-shadow: 0 0 0px #fff;
}

.custom-header .nav-icon:hover i,
.custom-header .nav-icon:focus i {
    text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff, 0 0 30px #ffffff;
    transform: scale(1.2);
}

/* Logo */
.custom-header .logo-tienda {
    height: 70px; 
    width: auto;
    max-width: 200px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.custom-header .logo-tienda:hover {
    transform: scale(1.05);
}

/* Buscador en el menú móvil */
#templatemo_main_nav .input-group {
    margin-left: auto;
    margin-right: auto;
    max-width: 300px; /* ancho máximo para que no se deforme */
}

/* Opcional: margen superior para que quede más alineado con los enlaces */
#templatemo_main_nav .input-group input {
    text-align: center; /* centramos el texto dentro */
}


/* Fondo negro y centrado en el menú responsive */
@media (max-width: 991px) {
    #templatemo_main_nav {
        background-color: #000000 !important; /* mismo negro del header */
        padding: 1rem 0; /* espacio arriba y abajo */
        text-align: center; /* centra los elementos */
    }

    #templatemo_main_nav .nav-item {
        margin: 0.5rem 0; /* espacio entre opciones */
    }

    #templatemo_main_nav .nav-link {
        color: #ffffff !important; /* blanco */
        font-size: 1.2rem;
        font-weight: 500;
    }

    #templatemo_main_nav .nav-link:hover {
        text-shadow: 0 0 10px #ffffff, 0 0 20px #ffffff;
    }
}
/* Dropdown móvil con fondo negro centrado (lo que ya tenías) */
@media (max-width: 991.98px) {
  #templatemo_main_nav {
    background-color: #000 !important;
    padding: 1rem 0;
    text-align: center;
  }
  #templatemo_main_nav .nav {
    flex-direction: column;
    align-items: center;
  }
  #templatemo_main_nav .nav-item { margin: .5rem 0; }
}

/* --- FIX buscador --- */
@media (max-width: 991.98px) {
  /* Wrapper del buscador (el div con d-lg-none col-7 col-sm-auto ...) */
  #templatemo_main_nav .d-lg-none {
    flex: 0 0 100% !important;    /* anula col-7 */
    max-width: 100% !important;   /* anula col-7 */
    padding-left: 1rem;           /* margen izquierdo visible */
    padding-right: 1rem;
    margin-top: .5rem;
    margin-bottom: .75rem;
  }

  /* Centra y limita el input dentro del wrapper */
  #templatemo_main_nav .d-lg-none .input-group {
    max-width: 320px;             /* evitá que se estire */
    margin-left: auto;
    margin-right: auto;           /* centrado */
  }

  #templatemo_main_nav .d-lg-none .form-control {
    text-align: center;
  }
}

/* Opcional: a partir de 768px podés achicar un poco más el buscador si querés */
@media (min-width: 768px) and (max-width: 991.98px) {
  #templatemo_main_nav .d-lg-none .input-group {
    max-width: 300px;
  }
}
/*Estilo para pantallas medianas en adelante */
@media (min-width: 768px) {
  /* Centramos el menú principal (Home, Tienda, Contactanos) */
  .navbar-nav {
    margin: 0 auto !important;   /* fuerza centrado */
    text-align: center;
  }

  /* Centramos el buscador */
  .navbar .form-inline {
    margin: 0 auto !important;
    max-width: 300px;            /* evita que se estire demasiado */
  }

  /* Aseguramos que carrito y logout no se vayan a las puntas */
  .navbar .ms-auto {
    margin-left: 0 !important;
  }
}
</style>

</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-light d-none d-lg-block" id="templatemo_nav_top">
        <div class="container text-light">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <i class="fa fa-envelope mx-2"></i>
                    <a class="navbar-sm-brand text-light text-decoration-none" href="mailto:info@company.com">liffdomotic@gmail.com</a>
                    <i class="fa fa-phone mx-2"></i>
                    <a class="navbar-sm-brand text-light text-decoration-none" href="tel:+5695654514545">+5695654514545</a>
                </div>
                <div>
                    <a class="text-light" href="https://fb.com/templatemo" target="_blank" rel="sponsored"><i class="fab fa-facebook-f fa-sm fa-fw me-2"></i></a>
                    <a class="text-light" href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram fa-sm fa-fw me-2"></i></a>
                    <a class="text-light" href="https://twitter.com/" target="_blank"><i class="fab fa-twitter fa-sm fa-fw me-2"></i></a>
                    <a class="text-light" href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin fa-sm fa-fw"></i></a>
                </div>
            </div>
        </div>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-dark custom-header shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">

        <a class="navbar-brand text-success logo h1 align-self-center" href="<?php echo get_site_url(); ?>">
            <img class="logo-tienda" src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" />
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <?php
                        $menu_items = wp_get_nav_menu_items('menu-principal');
                        foreach ((array) $menu_items as $key => $menu_item) { // Iterar sobre los elementos del menú
                            if (!$menu_item->menu_item_parent) { // Si no es un submenú
                        ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo $menu_item->url ?>" title="<?php echo $menu_item->title ?>"> <?php echo $menu_item->title ?></a>
                                </li>
                            <?php
                            }
                        }
                        //cargamos más menús
                    if(is_user_logged_in()){
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_site_url();?>/perfil" title="<?php echo $userdata->user_firstname." ".$userdata->user_lastname?>"><?php echo $userdata->user_firstname." ".$userdata->user_lastname?></a>
                        </li>
                        <?php
                    }else{
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_site_url();?>/login"  title="Login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_site_url();?>/registro"  title="Registro">Registro</a>
                        </li>

                        <?php
                    }
                        ?>
                    </ul>
                </div>
                <div class="navbar align-self-center d-flex">
                    <div class="d-lg-none flex-sm-fill mt-3 mb-4 col-7 col-sm-auto pr-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputMobileSearch" placeholder="Search ...">
                            <div class="input-group-text">
                                <i class="fa fa-fw fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <a class="nav-icon d-none d-lg-inline" href="#" data-bs-toggle="modal" data-bs-target="#templatemo_search">
                        <i class="fa fa-fw fa-search text-dark mr-2"></i>
                    </a>

                    <?php
                    if (is_user_logged_in()) {
                    ?>
                        <a class="nav-icon position-relative text-decoration-none" href="<?php echo get_site_url();?>/checkout">
                            <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                            <?php
                        if($carro[0]->cuantos>=1){
                            ?>
                        <span class="position-absolute top-0 left-100 translate-middle badge rounded-pill bg-light text-dark"><?php echo $carro[0]->cuantos;?></span>
                            <?php
                        }
                        ?>
                    </a>
                        <a class="nav-icon position-relative text-decoration-none" href="javascript:void(0);" onclick="cerrarSesion('<?php echo wp_logout_url(home_url('login')) ?>');" title="Cerrar Sesión">
                            <i class="fas fa-power-off text-dark mr-3"></i>
                        <?php
                    }
                        ?>
                        </a>
                </div>
            </div>

        </div>
    </nav>
    <!--modal-->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Cerrar"></button>
            </div>
            <form action="<?php echo get_site_url(); ?>" method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="s" placeholder="Buscar" />
                    <button type="submit" class="input-group-text bg-success text-light" title="Buscar">
                        <i class="fa fa-fw fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!--/modal-->