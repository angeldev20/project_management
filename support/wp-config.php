<?php

if ( file_exists( dirname( __FILE__ ) . '/../constants.php' ) ) {
	require_once dirname( __FILE__ ) . '/../constants.php';
}

if ( ! defined( 'WP_DB_NAME' ) ) {
	define( 'WP_DB_NAME', 'platform_wordpress' );
}

if ( ! defined( 'WP_DB_HOST' ) ) {
	define( 'WP_DB_HOST', 'localhost' );
}

if ( ! defined( 'WP_DB_USER' ) ) {
	define( 'WP_DB_USER', 'root' );
}

if ( ! defined( 'WP_DB_PW' ) ) {
	define( 'WP_DB_PW', '' );
}

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', WP_DB_NAME );

/** MySQL database username */
define( 'DB_USER', WP_DB_USER );

/** MySQL database password */
define( 'DB_PASSWORD', WP_DB_PW );

/** MySQL hostname */
define( 'DB_HOST', WP_DB_HOST );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'oG@@GC0,J.pf;;OFSZl&+4d`NNqRCz77y2dt,>n&c}ab@`}^XDu[q5}*AYHBf0)l' );
define( 'SECURE_AUTH_KEY', '}$Poha:rS>_<B&B|tl=6I1}|Q@Nd%b7j&z}&Hx%ZhD,7e>p,(C7:m%/;)BsO*-7y' );
define( 'LOGGED_IN_KEY', '8`20d1al^DW+)K+Pi4vB0W!u`BL (N}uTRjB:M8N>~or1z>%Y 6s[.V|%3D[j.}V' );
define( 'NONCE_KEY', 'M[wkWNXahE)Z,<!%.9zT=1C1.m`v]JJ=oMUtRxN:(;)WfsNkdVpixZONt_oyVxc~' );
define( 'AUTH_SALT', 'il#%p(nG_X.kHJKowS<Jn|)&rcY%jwxnBe^5;jjgcV&=BG*Kgy}D>&A^>Tc.@SlL' );
define( 'SECURE_AUTH_SALT', 'IH;y/e)tx;Q0#|0tadCAYZ$YAt5+b8thgRy-2K.&/;,P#J28~NDS1axu4uonhOD~' );
define( 'LOGGED_IN_SALT', '}L=$&mqx Pu,UWQ`O|Pc,Z|V_Yvs4lm49I(ZiNY%xd3c*z# YFPXXi:T0~DkD$*Q' );
define( 'NONCE_SALT', 'kFQ6JuO MjyonF8r0[xQiIUJj)w[[yTi&^X^ As$$jj|QZ0=cTH|E+,CBpqJjvv|' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
