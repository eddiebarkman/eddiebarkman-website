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
define('DB_NAME', 'eddiebar_wp668');

/** MySQL database username */
define('DB_USER', 'eddiebar_wp668');

/** MySQL database password */
define('DB_PASSWORD', '4@5SpKa)5L');

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
define('AUTH_KEY',         '2wev2og7votzn77qpxobvonbersz8xm429amm8r2w4eaxilqlzkn9e4fd7fghvfq');
define('SECURE_AUTH_KEY',  'bqh5vrbpopdxfezeq52qk6qp77nmyqgy1zzfzlfevxcl2ydobkrwg8ucgqdxsa6w');
define('LOGGED_IN_KEY',    'e2m9rjfs3mufbdzqgdb8xhnrnmlt2uul4xmixzqsumtssiqm8raxi32usk7xklcj');
define('NONCE_KEY',        'uysw62usavfpv22ztxmajhr7aoz1mhplqj5qwnijo2vtledewekytqm0gfaxfj78');
define('AUTH_SALT',        'o1z3q6fohgcbsijly15oa8mtsnqtnhcixwsfphkumt1bl183rpitq5np9fzrmw8k');
define('SECURE_AUTH_SALT', 'gwmqxnanhsezbj4rmkjamjytsvnskhi5b53o0k27db5izbv7zfkr9cdjhyyjjslb');
define('LOGGED_IN_SALT',   'we30xa1khe9xct5furxskrhiv5jtz5sygzzxzvgthicmz2fmybm6oklt6edilewt');
define('NONCE_SALT',       's2xmmzexa1a7pr169wn3tvxwrhwbvirltssrhn3geezlpe7vm8sckuppn2pft7io');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wphl_';

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
