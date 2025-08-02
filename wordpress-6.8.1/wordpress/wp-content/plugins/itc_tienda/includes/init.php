<?php 
if(!defined('ABSPATH')) die();
require_once plugin_dir_path( __FILE__ ) . 'pasarelas.php';
require_once plugin_dir_path( __FILE__ ) . 'variables_globales.php';
if(!function_exists('itc_tienda_init')){
    function itc_tienda_init(){
        global $wpdb;
        $estadosql="create table if not exists 
            {$wpdb->prefix}itc_tienda_carro_estado
            (
            id int not null auto_increment,
            nombre varchar(100) not null, 
            primary key (id)
            )";
        $wpdb->query($estadosql);
        $estadosqlcreate="insert into {$wpdb->prefix}itc_tienda_carro_estado values (1, 'Creado'), (2, 'Cancelado'), (3, 'En Tránsito'), (4, 'Entregado'), (5, 'Pagado'), (6, 'Pendiente de pago');";
        $wpdb->query($estadosqlcreate);
        $carro ="create table if not exists 
        {$wpdb->prefix}itc_tienda_carro
        (
        id int not null auto_increment,
        usuario_id int not null,
        estado_id int not null,
        ciudad varchar(100) default 0,
        direccion text ,
        telefono varchar(100) default 0,
        observaciones text ,
        fecha date,
        fecha_final date,
        tipo_pago int default 0,
        token varchar(255) default 0,
        monto int default 0,
        primary key (id)
        ); 
        ";  
        $wpdb->query($carro);

        $fk="alter table {$wpdb->prefix}itc_tienda_carro add constraint fk_itc_tienda_carro_estado_id foreign key (estado_id) references {$wpdb->prefix}itc_tienda_carro_estado(id);";
        $wpdb->query($fk);
        $carroDetalle ="create table if not exists 
        {$wpdb->prefix}itc_tienda_carro_detalle
        (
        id int not null auto_increment,
        itc_tienda_carro_id int not null,
        producto_id int not null,
        cantidad int,
        primary key (id)
        ); 
        ";
        $wpdb->query($carroDetalle);
        $fk2="alter table {$wpdb->prefix}itc_tienda_carro_detalle add constraint fk_itc_tienda_carro_id foreign key (itc_tienda_carro_id) references {$wpdb->prefix}itc_tienda_carro(id);";
        $wpdb->query($fk2);

        //pasarelas
        $pasarelas="create table if not exists 
            {$wpdb->prefix}itc_tienda_carro_pasarelas
            (
            id int not null auto_increment,
            nombre varchar(100) not null, 
            estado_id int not null default 0,
            descripcion text,
            url varchar(255) not null, 
            cliente_id varchar(255) not null,
            cliente_secret varchar(255) not null,
            primary key (id)
            )";
        $wpdb->query($pasarelas);
        $estadosqlcreate="insert into {$wpdb->prefix}itc_tienda_carro_pasarelas values (1, 'Webpay de Transbank', 0, 'Pasarela de pago argentina que permite pagar en pesos argentinos', '', '', ''), (2, 'Paypal', 0, 'Pasarela de pago internacional que permite pagar en dólares y euros', '', '', ''), (3, 'Mercado Pago', 0, 'Pasarela de pago internacional que permite pagar pesos argentinos', '', '', ''),  (4, 'Stripe', 0, 'Pasarela de pago internacional', '', '', '');";
        $wpdb->query($estadosqlcreate);
        $wpdb->query("create table if not exists {$wpdb->prefix}itc_tienda_variables_globales (
            id int not null auto_increment, 
            nombre varchar(100)  ,
            valor text,
            primary key (id)
        );");
        $wpdb->query("insert into {$wpdb->prefix}itc_tienda_variables_globales values (1, 'smtp_server', ''), (2, 'smtp_user', ''), (3, 'smtp_password', ''),  (4, 'smtp_port', ''),  (5, 'secret', '');");
        $wpdb->query("create table if not exists {$wpdb->prefix}itc_tienda_pais (
            id int not null auto_increment, 
            nombre varchar(100)  ,
            primary key (id)
        );");
        $wpdb->query("insert into {$wpdb->prefix}itc_tienda_pais values ('1', 'Afganistán'),
        ('2', 'Islas Gland'),
        ('3', 'Albania'),
        ('4', 'Alemania'),
        ('5', 'Andorra'),
        ('6', 'Angola'),
        ('7', 'Anguilla'),
        ('8', 'Antártida'),
        ('9', 'Antigua y Barbuda'),
        ('10', 'Antillas Holandesas'),
        ('11', 'Arabia Saudí'),
        ('12', 'Argelia'),
        ('13', 'Argentina'),
        ('14', 'Armenia'),
        ('15', 'Aruba'),
        ('16', 'Australia'),
        ('17', 'Austria'),
        ('18', 'Azerbaiyán'),
        ('19', 'Bahamas'),
        ('20', 'Bahréin'),
        ('21', 'Bangladesh'),
        ('22', 'Barbados'),
        ('23', 'Bielorrusia'),
        ('24', 'Bélgica'),
        ('25', 'Belice'),
        ('26', 'Benin'),
        ('27', 'Bermudas'),
        ('28', 'Bhután'),
        ('29', 'Bolivia'),
        ('30', 'Bosnia y Herzegovina'),
        ('31', 'Botsuana'),
        ('32', 'Isla Bouvet'),
        ('33', 'Brasil'),
        ('34', 'Brunéi'),
        ('35', 'Bulgaria'),
        ('36', 'Burkina Faso'),
        ('37', 'Burundi'),
        ('38', 'Cabo Verde'),
        ('39', 'Islas Caimán'),
        ('40', 'Camboya'),
        ('41', 'Camerún'),
        ('42', 'Canadá'),
        ('43', 'República Centroafricana'),
        ('44', 'Chad'),
        ('45', 'República Checa'),
        ('46', 'Chile'),
        ('47', 'China'),
        ('48', 'Chipre'),
        ('49', 'Isla de Navidad'),
        ('50', 'Ciudad del Vaticano'),
        ('51', 'Islas Cocos'),
        ('52', 'Colombia'),
        ('53', 'Comoras'),
        ('54', 'República Democrática del Congo'),
        ('55', 'Congo'),
        ('56', 'Islas Cook'),
        ('57', 'Corea del Norte'),
        ('58', 'Corea del Sur'),
        ('59', 'Costa de Marfil'),
        ('60', 'Costa Rica'),
        ('61', 'Croacia'),
        ('62', 'Cuba'),
        ('63', 'Dinamarca'),
        ('64', 'Dominica'),
        ('65', 'República Dominicana'),
        ('66', 'Ecuador'),
        ('67', 'Egipto'),
        ('68', 'El Salvador'),
        ('69', 'Emiratos Árabes Unidos'),
        ('70', 'Eritrea'),
        ('71', 'Eslovaquia'),
        ('72', 'Eslovenia'),
        ('73', 'España'),
        ('74', 'Islas ultramarinas de Estados Unidos'),
        ('75', 'Estados Unidos'),
        ('76', 'Estonia'),
        ('77', 'Etiopía'),
        ('78', 'Islas Feroe'),
        ('79', 'Filipinas'),
        ('80', 'Finlandia'),
        ('81', 'Fiyi'),
        ('82', 'Francia'),
        ('83', 'Gabón'),
        ('84', 'Gambia'),
        ('85', 'Georgia'),
        ('86', 'Islas Georgias del Sur y Sandwich del Sur'),
        ('87', 'Ghana'),
        ('88', 'Gibraltar'),
        ('89', 'Granada'),
        ('90', 'Grecia'),
        ('91', 'Groenlandia'),
        ('92', 'Guadalupe'),
        ('93', 'Guam'),
        ('94', 'Guatemala'),
        ('95', 'Guayana Francesa'),
        ('96', 'Guinea'),
        ('97', 'Guinea Ecuatorial'),
        ('98', 'Guinea-Bissau'),
        ('99', 'Guyana'),
        ('100', 'Haití'),
        ('101', 'Islas Heard y McDonald'),
        ('102', 'Honduras'),
        ('103', 'Hong Kong'),
        ('104', 'Hungría'),
        ('105', 'India'),
        ('106', 'Indonesia'),
        ('107', 'Irán'),
        ('108', 'Iraq'),
        ('109', 'Irlanda'),
        ('110', 'Islandia'),
        ('111', 'Israel'),
        ('112', 'Italia'),
        ('113', 'Jamaica'),
        ('114', 'Japón'),
        ('115', 'Jordania'),
        ('116', 'Kazajstán'),
        ('117', 'Kenia'),
        ('118', 'Kirguistán'),
        ('119', 'Kiribati'),
        ('120', 'Kuwait'),
        ('121', 'Laos'),
        ('122', 'Lesotho'),
        ('123', 'Letonia'),
        ('124', 'Líbano'),
        ('125', 'Liberia'),
        ('126', 'Libia'),
        ('127', 'Liechtenstein'),
        ('128', 'Lituania'),
        ('129', 'Luxemburgo'),
        ('130', 'Macao'),
        ('131', 'ARY Macedonia'),
        ('132', 'Madagascar'),
        ('133', 'Malasia'),
        ('134', 'Malawi'),
        ('135', 'Maldivas'),
        ('136', 'Malí'),
        ('137', 'Malta'),
        ('138', 'Islas Malvinas'),
        ('139', 'Islas Marianas del Norte'),
        ('140', 'Marruecos'),
        ('141', 'Islas Marshall'),
        ('142', 'Martinica'),
        ('143', 'Mauricio'),
        ('144', 'Mauritania'),
        ('145', 'Mayotte'),
        ('146', 'México'),
        ('147', 'Micronesia'),
        ('148', 'Moldavia'),
        ('149', 'Mónaco'),
        ('150', 'Mongolia'),
        ('151', 'Montserrat'),
        ('152', 'Mozambique'),
        ('153', 'Myanmar'),
        ('154', 'Namibia'),
        ('155', 'Nauru'),
        ('156', 'Nepal'),
        ('157', 'Nicaragua'),
        ('158', 'Níger'),
        ('159', 'Nigeria'),
        ('160', 'Niue'),
        ('161', 'Isla Norfolk'),
        ('162', 'Noruega'),
        ('163', 'Nueva Caledonia'),
        ('164', 'Nueva Zelanda'),
        ('165', 'Omán'),
        ('166', 'Países Bajos'),
        ('167', 'Pakistán'),
        ('168', 'Palau'),
        ('169', 'Palestina'),
        ('170', 'Panamá'),
        ('171', 'Papúa Nueva Guinea'),
        ('172', 'Paraguay'),
        ('173', 'Perú'),
        ('174', 'Islas Pitcairn'),
        ('175', 'Polinesia Francesa'),
        ('176', 'Polonia'),
        ('177', 'Portugal'),
        ('178', 'Puerto Rico'),
        ('179', 'Qatar'),
        ('180', 'Reino Unido'),
        ('181', 'Reunión'),
        ('182', 'Ruanda'),
        ('183', 'Rumania'),
        ('184', 'Rusia'),
        ('185', 'Sahara Occidental'),
        ('186', 'Islas Salomón'),
        ('187', 'Samoa'),
        ('188', 'Samoa Americana'),
        ('189', 'San Cristóbal y Nevis'),
        ('190', 'San Marino'),
        ('191', 'San Pedro y Miquelón'),
        ('192', 'San Vicente y las Granadinas'),
        ('193', 'Santa Helena'),
        ('194', 'Santa Lucía'),
        ('195', 'Santo Tomé y Príncipe'),
        ('196', 'Senegal'),
        ('197', 'Serbia y Montenegro'),
        ('198', 'Seychelles'),
        ('199', 'Sierra Leona'),
        ('200', 'Singapur'),
        ('201', 'Siria'),
        ('202', 'Somalia'),
        ('203', 'Sri Lanka'),
        ('204', 'Suazilandia'),
        ('205', 'Sudáfrica'),
        ('206', 'Sudán'),
        ('207', 'Suecia'),
        ('208', 'Suiza'),
        ('209', 'Surinam'),
        ('210', 'Svalbard y Jan Mayen'),
        ('211', 'Tailandia'),
        ('212', 'Taiwán'),
        ('213', 'Tanzania'),
        ('214', 'Tayikistán'),
        ('215', 'Territorio Británico del Océano Índico'),
        ('216', 'Territorios Australes Franceses'),
        ('217', 'Timor Oriental'),
        ('218', 'Togo'),
        ('219', 'Tokelau'),
        ('220', 'Tonga'),
        ('221', 'Trinidad y Tobago'),
        ('222', 'Túnez'),
        ('223', 'Islas Turcas y Caicos'),
        ('224', 'Turkmenistán'),
        ('225', 'Turquía'),
        ('226', 'Tuvalu'),
        ('227', 'Ucrania'),
        ('228', 'Uganda'),
        ('229', 'Uruguay'),
        ('230', 'Uzbekistán'),
        ('231', 'Vanuatu'),
        ('232', 'Venezuela'),
        ('233', 'Vietnam'),
        ('234', 'Islas Vírgenes Británicas'),
        ('235', 'Islas Vírgenes de los Estados Unidos'),
        ('236', 'Wallis y Futuna'),
        ('237', 'Yemen'),
        ('238', 'Yibuti'),
        ('239', 'Zambia'),
        ('240', 'Zimbabue');
        ");
    }
}
if(!function_exists('itc_tienda_init_eliminar')){
    function itc_tienda_init_eliminar(){
        global $wpdb; 
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_variables_globales");
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_carro_pasarelas");
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_carro_detalle");
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_carro");
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_carro_estado");
        $wpdb->query("drop table {$wpdb->prefix}itc_tienda_pais;");
    }
}

###cargamos el menú
if(!function_exists('itc_tienda_init_crear_menu')){
    add_action('admin_menu', 'itc_tienda_init_crear_menu');
    function itc_tienda_init_crear_menu(){
        add_menu_page( 
            "ITC Variables", 
            "ITC Variables", 
            "manage_options", 
            plugin_dir_path( __FILE__ )."admin/variables_globales.php", 
            null, 
            'dashicons-admin-tools', 
            132 );
        add_menu_page( 
            "ITC Pasarelas", 
            "ITC Pasarelas", 
            "manage_options", 
            plugin_dir_path( __FILE__ )."admin/pasarelas.php", 
            null, 
            'dashicons-image-rotate-left', 
            133 );
        add_menu_page( 
                "ITC Slide", 
                "ITC Slide",
                "manage_options",  
                plugin_dir_path( __FILE__ )."admin/slide_listar.php", 
                null, 
                'dashicons-slides', 
                134 );
        add_submenu_page( 
                    null, 
                    "Editar", //Título del menú
                    null, //título de la página
                    "manage_options", 
                    plugin_dir_path( __FILE__ )."admin/slide_editar.php", 
                    null  ); 
        add_menu_page( 
            "ITC Tienda Ventas", 
            "ITC Tienda Ventas", 
            "manage_options", 
            plugin_dir_path( __FILE__ )."admin/ventas.php", 
            null, 
            'dashicons-list-view', 
            137 ); 
        add_submenu_page( 
                null, 
                "Excel", //Título del menú
                null, //título de la página
                "manage_options", 
                plugin_dir_path( __FILE__ )."/excel.php", 
                null  );  
        }
          
    }