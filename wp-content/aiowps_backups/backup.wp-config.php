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
define('DB_NAME', 'forwodians_nl_2');

/** MySQL database username */
define('DB_USER', 'forwodians');

/** MySQL database password */
define('DB_PASSWORD', 'Xq3e8SHxE4');

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
define('AUTH_KEY',         'So*&3Yt.jOawq.4K]c</4}<10iNFS*0f.Zt{7_MUT-OQ#N]oD@8YK Ms9h]m:G)L');
define('SECURE_AUTH_KEY',  'WGQ9vV;!ms]xof)XsDmNetEI!kz%B7J1J@]%Bo)MzXHd~bV1$s>e^&IcI|  s=g-');
define('LOGGED_IN_KEY',    ':sKgM1dnIRRvbl>iF1_x[hzVGI2/Fxf&o6h.ZGR7[a>5aq%&>y0gDUwxQYILwi`!');
define('NONCE_KEY',        '2fF0bd.Y8e]#31>zo:|FHvC~sZ{xki*%7B5*ls(ye+p1Xnmzo^sVURB0G3l+X<Cm');
define('AUTH_SALT',        'wGHgxqiK*zNH9Cf},7mvK`XVs[~Tn#pdWvvF2]GGs69xwID3_ OAYSwC^$ypC8#i');
define('SECURE_AUTH_SALT', 'EKdyj<}<96&H1sVjsYnc4L27A1ir@)2<v(u1%2NMXD6*C1gz-Bndq0q]`qz[^x1j');
define('LOGGED_IN_SALT',   '.dV76&nk`Ne3y{dmhiG.E%9-Nh4+.wJ`~7OK5MmTZm!zWJ{=>$f/IBiRq-nGbvgA');
define('NONCE_SALT',       'QB^iu>N.ER27R/&|_Se-AkkuxBN* OQ91Dc4$O1{Z[xHyPIQzQ)49=i@Zx+RHN-?');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'lgtlv_';

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
