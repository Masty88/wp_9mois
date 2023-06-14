<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'neuf_mois' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '8cT:xIDD2edjt]KPt=,PIQ>ICLLDq-d-oLVxFoyD$GAgA}pQjo4(~vFCQtRho4q<' );
define( 'SECURE_AUTH_KEY',  '39)jpWlFh|KFL~C)4iCKxE/,U$i!1Kj6oex_{`NOpz]OvSq%z5N.pE:-TM#RD(_}' );
define( 'LOGGED_IN_KEY',    '7@%)v`?yGnWo&p0-oBCNYmliT<}<El)P4{A&H]>}Bb6y`szbl<GQ<):(/cy`#2n4' );
define( 'NONCE_KEY',        'o9 9 #nPYm~Ov=vTIzM9y}Sh5%Qs#:oj$=qf65/e|7(Oej&<2`:rl5NiJE<ZSY.X' );
define( 'AUTH_SALT',        '5i;n5F~Gqq7RM+BqrF8;ssq&tNfR.1Z^/KTdbwFR ;PLCmxF:Y<:@=>?F]8d]H>m' );
define( 'SECURE_AUTH_SALT', '@EauR~|S_N|{gJtG?OCwwS P@DZX?2W8+2L&L8m+Y?:3$U1=qYn!MHA.cG/2e4S0' );
define( 'LOGGED_IN_SALT',   '$xO/9B x;Qvb/m4kuWt2OYYr#IgG5=xi~OO5eZ@b1Hwpx9RY:69^9Myy*^XJTyQ ' );
define( 'NONCE_SALT',       'iOr`,3WaGusG4?MwZb{Be: E4m)0z3Bn{H8g_!x]bTImVh4kGMq7mv=aaU?|*?1y' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wc';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
