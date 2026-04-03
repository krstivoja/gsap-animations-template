<?php
/**
 * Plugin Name:       Fancoolo FX
 * Plugin URI:        https://github.com/krstivoja/fancoolo-fx
 * Description:       A class-driven GSAP animation wrapper. Add CSS classes in Gutenberg and get animations — no JavaScript needed.
 * Requires at least: 6.3.0
 * Requires PHP:      7.4
 * Version:           1.6.1
 * Author:            devusrmk
 * Author URI:        https://github.com/krstivoja
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fancoolo-fx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_prefix = 'FANCOOLOFX';

$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );

define( $plugin_prefix . '_DIR', plugin_basename( __DIR__ ) );
define( $plugin_prefix . '_BASE', plugin_basename( __FILE__ ) );
define( $plugin_prefix . '_PATH', plugin_dir_path( __FILE__ ) );
define( $plugin_prefix . '_URL', plugin_dir_url( __FILE__ ) );
define( $plugin_prefix . '_VER', $plugin_data['Version'] );
define( $plugin_prefix . '_CACHE_KEY', 'fancoolo_fx-cache-key-for-plugin' );
define( $plugin_prefix . '_REMOTE_URL', 'https://selfhost.dplugins.com/wp-content/plugins/hoster/inc/secure-download.php?file=json&download=51&token=77864ea0f9aba39e3730f481e49cea418a4825d9665370e982b227969e42fb3a' );

// Backwards-compatible constants used by inc/ classes.
define( 'FANCOOLO_FX_VERSION', FANCOOLOFX_VER );
define( 'FANCOOLO_FX_PATH', FANCOOLOFX_PATH );
define( 'FANCOOLO_FX_URL', FANCOOLOFX_URL );

// Load classes.
require_once FANCOOLOFX_PATH . 'inc/Settings.php';
require_once FANCOOLOFX_PATH . 'inc/Frontend.php';
require_once FANCOOLOFX_PATH . 'inc/Editor.php';
require_once FANCOOLOFX_PATH . 'inc/Admin.php';
require_once FANCOOLOFX_PATH . 'inc/AdminPage.php';
require_once FANCOOLOFX_PATH . 'inc/SaveHandler.php';
require_once FANCOOLOFX_PATH . 'inc/update.php';

// Wire hooks.
add_action( 'wp_enqueue_scripts', array( FancooloFX\Frontend::class, 'enqueue' ) );
add_action( 'enqueue_block_editor_assets', array( FancooloFX\Editor::class, 'enqueue' ) );
add_action( 'admin_menu', array( FancooloFX\Admin::class, 'register_menu' ) );
add_action( 'admin_enqueue_scripts', array( FancooloFX\Admin::class, 'enqueue' ) );
add_action( 'admin_init', array( FancooloFX\SaveHandler::class, 'handle' ) );

// Plugin updater.
new FANCOOLOFX_DPUpdateChecker(
	FANCOOLOFX_BASE,
	FANCOOLOFX_VER,
	FANCOOLOFX_CACHE_KEY,
	FANCOOLOFX_REMOTE_URL
);
