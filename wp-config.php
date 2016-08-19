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
define('DB_NAME', 'hoteru');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '{xrnUx%dQ ]ApYYFb*G9=,.qL@)Punlm45k:ASs(>*hq}q}(wU}xSPfx]?{q#EEH');
define('SECURE_AUTH_KEY',  'xcl>2`%dDmrg5ha>|?[{e]=%6:vK9R%(=FpDqJ:}oR$U>UJ7khbub53iV]+kCpxS');
define('LOGGED_IN_KEY',    '._@kykq,gA+nY%dFpsHXNQc)t>WsS<:sO;jB{Y`g;NV4#Qp[_*ja/<,oVRu@{6py');
define('NONCE_KEY',        'w*rmJ+Gj4+CQl&]Januy#qhr(mDK.Dbbdm=[Q^HD`9_Xs%n r5b.Guw Pf).Kvf+');
define('AUTH_SALT',        '-4bLn^Xi!qwt?(73A.?c-x(5S11pmiwV nVm~#KOE|U n7kqm3eVf!ycFuqGgCJq');
define('SECURE_AUTH_SALT', 'g,GEmtZ8{9 weK(9D*6w1?NtGLI>(2Y&f8~$8 1G#q$ _6d5X#.$VCHp?=n_+)Aa');
define('LOGGED_IN_SALT',   '^?YLH.ODmarqN%&$(k`](?,MeMw*aEvN.HOh)z^IlND^vQmkjX]qt4kN2Os`r[a}');
define('NONCE_SALT',       '|r:EhHNQkxNnD{ok<ZACxRa)eEj!w_N4Qu5t_[D4<=8t2~X1K;A^EuZi/%;q:M|Z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
