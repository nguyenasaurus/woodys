<?php
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
define( 'DB_NAME', 'woodys_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'pbyp/z`<^OxYhGz5`k=;0b1>6]!1-a^*Q8Gr.+)Gs@1}Ures[jHo`>EW{BDH0bXF' );
define( 'SECURE_AUTH_KEY',  '/|w)*>+iZfHWo|vv1RneGG3c^a)Z)wy%ep|r9]6n$s&0C4|.=0J+v_/gkO9cRK.y' );
define( 'LOGGED_IN_KEY',    'qa|vJh=%#CFZ9x.o~v9^ 57bc6)t`UB,jr}+ugt/zRIzK$=G[ph?V-eGbE_<+,]5' );
define( 'NONCE_KEY',        '/T-eivCuU?-U*9[:6[kzjLJFg?3@]!KEKB|EdN~P~rCPj)?v|Hjijc^asRjC%v4>' );
define( 'AUTH_SALT',        'Mmh-<uK6%U)vU/5LklM#%Wmhck(HUzkR7EF3AK;s8YEfvF4#!3&X&c=V|fI4P~1)' );
define( 'SECURE_AUTH_SALT', 'r4CwAE86Z`7]PI)zU#i<s}}Q^W=uu~y] z@i>d{MY5p}g}nKa9$#NMm)>R^W=YnL' );
define( 'LOGGED_IN_SALT',   '5(&>& h7P%Xdu(oL22a:_fYfrP:%;zWe0b1$St1`2pQy9==T)MHZ-`0`-504EW%Z' );
define( 'NONCE_SALT',       'NdKZ[5N:6D1{;<XpViG+1Mek--k:c@VjSe%)qS6lgG|}GDaQ[)ts)Wz3mkr3$JU:' );

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
