<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db_ecommerce' );

/** Database username */
define( 'DB_USER', 'lucho' );

/** Database password */
define( 'DB_PASSWORD', 'lnf91218' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '>G|X2;^oJw@;Qu~D1y#{#k@M4(o#{rj^-8gd^A_!7DIRv^Nl^cc>ZWw54IAGU4<n' );
define( 'SECURE_AUTH_KEY',  'l5nGy#F:xf#T]$.YgK?GxPcO(ESr[)V-xlSF=f0NC!.0GdJj^E+o6WuY=J?@7<$Y' );
define( 'LOGGED_IN_KEY',    '%bZ;kSm.lu~10>9_D+qG.Ypf@b_#)_,|jHoo<f1m&%_-`0DsFI{g-RZzGq#!pHF1' );
define( 'NONCE_KEY',        'L[!{J<ZB0sx+k].:ax_<w(?V45/~C+8)To+8_)+>MI_8]82@jIzXUtM>4z30q7#{' );
define( 'AUTH_SALT',        'zSSfJh{|qlhI0Iai>9Pb EiI#rO8w1bUcm_ha>cr|@g1Qna2>VqF?}8H9~T@ K^b' );
define( 'SECURE_AUTH_SALT', 'N+;xdj=;&YZs{2TT 4p!nCk23g{pl^C`Jvt&yzBSu1~RLU)D83T(]EATQaQ7bNS}' );
define( 'LOGGED_IN_SALT',   '%(,AL$P a4~p%s.*Ub0;12]vF7$HI0Oa3^cd:3cYqBX_l(}0J{3CJR_E%RiT:hyY' );
define( 'NONCE_SALT',       '$Z`,rprb%F+&oFn|(sWN@ohon?+cG7fI?i&>O&$g::Sk 6w2&X&{@?f.d,1Lf/,O' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
