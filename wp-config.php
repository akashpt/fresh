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
define( 'DB_NAME', 'fresh' );
// define( 'DB_NAME', 'u946952377_pureaura' );

/** Database username */
define( 'DB_USER', 'root' );
// define( 'DB_USER', 'u946952377_pureaura' );

/** Database password */
define( 'DB_PASSWORD', '' );
// define( 'DB_PASSWORD', 'U*VytKXW7p*' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1:3307' );
// define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'r?C*:|f^?mnH!)bD[)}5&J%XN</J3^N[{BJ|@;)i@}OfIF-D`t$VWl57au&}6p&7' );
define( 'SECURE_AUTH_KEY',  '<ga6>G%Ril3y8>lebV&WmfXtw~(/o>EmYvB,[ +F/<ZLK4i)Ec98F%x(Bd<=U?@&' );
define( 'LOGGED_IN_KEY',    '5w~-WGpi+0+Lh2,tDqLf_wh{7HO)]1uHX4/7[4Tt3{,Nb,rxfI*f6`E{Y^)Zz|J`' );
define( 'NONCE_KEY',        'pinVBk5*XqZiqep^Z=u]ME]@b%1p= QJJpd{QqF5`jEB4pr&5/]VO5#SiQc><jb.' );
define( 'AUTH_SALT',        'JJ),2sDI Ql!N8A Q_^]1rxa}[<yDf=,F+yaZ!%aXvjTB$fK*5TLi+=dG;bAQ=N!' );
define( 'SECURE_AUTH_SALT', ':SGHO&xMkD{o^Vulz,+a _>QUrXOHmbdglFYTTkTba_mL.Ps!qw@vn.D0LF;u%p%' );
define( 'LOGGED_IN_SALT',   '4.:It#00zOQ@%2{UfLokKm!sp(p/g;m:/sa;j|rqRUo=sgYn5C91`J##Qx= CQzo' );
define( 'NONCE_SALT',       'c;A.uIrqLO&cF !>{g3D^eEUV J9.k6>1+q8ahCaXf!rsZW|AA!LO#hNC%$IS{aC' );

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
