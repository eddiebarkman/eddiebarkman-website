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
define('DB_NAME', 'eddiebar_wp387');

/** MySQL database username */
define('DB_USER', 'eddiebar_wp387');

/** MySQL database password */
define('DB_PASSWORD', 'q!Cp7S31@k');

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
define('AUTH_KEY',         'npztngv959vhzcrfv0nynrfpfb1skxx8rmhnxikdhy5auoenokco4bppyioepj2s');
define('SECURE_AUTH_KEY',  '5vsaqcb2voxxiwepuaei1vrynfbmtvbi1jr0gvreay4t4j5nddn9tkikaef7sr5z');
define('LOGGED_IN_KEY',    'kraramf0erjfeln5vtyio7dgyldrmt5dekt0fs2uqcnuifg1yc0zmkjb4iifq8c8');
define('NONCE_KEY',        'ocnegwizoj2furszwsn0c6a0njgyyrqrmtk9twzbs1curslbeh151phrgkziiyj3');
define('AUTH_SALT',        'it5yaexstks81lf1bct1uzpq6stl9yppcieputd6qusclmeyjzmggjccnoszfsyw');
define('SECURE_AUTH_SALT', 'ctm9skkodmie5anc9u2blogr9m2ahzoqhg2vn9po5jvv65sc12128utndi4dsk4a');
define('LOGGED_IN_SALT',   'ma1gybzmk5hguoz8s19f0xakdtalkldjwnsqcu45cswshdbvuvorv4vvkcqcq6yv');
define('NONCE_SALT',       '92ycxnc9hfqdimihtp4y1e2xxdpw0yybowp2ajsus1wbucjyrkvgfmbz8mwa5wpa');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpxv_';

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
