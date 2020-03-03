<?php

/**
 * Patch para habilitar HTTPS e Multi Site
 */
$uh_protocol = 'http';
$uh_forwarded_proto = $_SERVER['HTTP_X_FORWARDED_PROTO'];
if (isset($uh_forwarded_proto) && 'https' == $uh_forwarded_proto) {
	$_SERVER['HTTPS'] = 'on';
	$uh_protocol = $uh_forwarded_proto;
}

$uh_wpconfig_dir = preg_replace('/\\\\/', '/', dirname(__FILE__));
$uh_wp_path = preg_replace('/(E:\/home\/[^\/]+\/[^\/]+\/|\/var\/www\/html\/[^\/]+\/)web(.*)/', '$2', $uh_wpconfig_dir);
if (!empty($_SERVER['HTTP_HOST'])) {
    $uh_site_url = $uh_protocol . '://' . $_SERVER['HTTP_HOST'] . $uh_wp_path;
    define('WP_SITEURL', $uh_site_url);
    define('WP_HOME', $uh_site_url);
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
define('DB_NAME', 'saojos');

/** MySQL database username */
define('DB_USER', 'saojos');

/** MySQL database password */
define('DB_PASSWORD', 'rJ9+1B2g');

/** MySQL hostname */
define('DB_HOST', 'saojos.mysql.uhserver.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'IYmYzk3tNc4TMcOidj(M@gra8aX5sy1lX21xE6Z1ObHz(7geb!LYuua9IDXWSyzV');
define('SECURE_AUTH_KEY',  'VGqDQQ4cQ(Acd0@%hSMSr9J6n(aTx58^xe4N(A5WSxTr4(R^7WA9a^8cAm*(vSfk');
define('LOGGED_IN_KEY',    '(dxK#oEQ&qmErvTQ^SrymcMzodK3lL4zZTBcd1a)Q*By1d*W*RgvY)xFJE7oz1x(');
define('NONCE_KEY',        '72iopjXGpGevY^29vXGXTVOD^E52ByCY4vBtS800vL)I#r9Cn#f(DtMJ%3cB^WW(');
define('AUTH_SALT',        'ROi05hpv^BR594lu7t9^pbC)U^WTK5OtIk7zImVuiq)0EDaoeyi6i8z*cbTjtAls');
define('SECURE_AUTH_SALT', '6UcWYZ#UIFkP@e7&oqGy&7&sIxkT3v06wc9Ck)NpGx#Te4AgEo4Ux7z(bx^roL@C');
define('LOGGED_IN_SALT',   'IYlWW6vRNzqUOkpR3%X1sB4klzXok8wLLCUr3mIWq9SXai3Iochfoz9w(&dmGWi2');
define('NONCE_SALT',       'e#8uZP*rQMdhy@0UmhXypaAFg6)3Cuo0t!iO3eUgPWaHJ%wr%ybJt)uD5@VNNhoy');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'apswp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');
?>