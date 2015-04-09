<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp');

/** MySQL database username */
define('DB_USER', 'wp');

/** MySQL database password */
define('DB_PASSWORD', 'qwerty');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         's:!1!y](r+;/T+QjSnT,B@<A?L%a$*7dN-emi&>5A4qB&`@VQ)cfRL;B?kA B[{i');
define('SECURE_AUTH_KEY',  'FB@A}tf0P=LbvM-~cL7r(;]|?|`a!76(,%(yI&&oqQn-5Pg0?#u@sRbdj,#Gr*~M');
define('LOGGED_IN_KEY',    'o7wAXfF-=m;9-t&.0*6gkocMf^p%PK]3|P9Y{=Qz;E4VNF+=aj4|4@8mwl>8eJ$2');
define('NONCE_KEY',        'XKNjn60/6cM?eq>s{c(*Pej,/7iNmSi+,fU7N+%Q0N}--0;+>$KL,<BCQ!-W87uN');
define('AUTH_SALT',        ',~T4e:tuSmD8)EG)6Q`,O@H:Xvc{+8v#,5_ )y`@9w&}|2$l;qzKFkOic^66BzqQ');
define('SECURE_AUTH_SALT', '0}*m;Geo+qRp{I[6IPKlrBDAfg4o<p>c8e}j-A%{~&1N*%b%K+>X9+]:CtgWDY?z');
define('LOGGED_IN_SALT',   '0|-7Fch$ikR(nk^/[/]/^hJMo*v@x06l+p`m>yHz6^o&0jCWKD::e}edJp+wE:5!');
define('NONCE_SALT',       '0ZGM$~yZQd_nMMWq|Hvo]gs4xbaRR1zUHr&3A8UWBVn*+tI2>ED8yx$LaaN7CDRi');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
