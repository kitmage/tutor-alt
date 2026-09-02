<?php
/**
 * Plugin Name: Tutor LMS
 * Plugin URI: https://tutorlms.com
 * Description: Build and manage professional online courses with unlimited lessons, a flexible quiz engine, and a complete student learning experience. No coding needed.
 * Author: Themeum
 * Version: 4.0.6
 * Author URI: https://themeum.com
 * Requires PHP: 7.4
 * Requires at least: 5.3
 * Tested up to: 7.0
 * License: GPLv2 or later
 * Text Domain: tutor
 * Update URI: https://github.com/kitmage/tutor-alt
 *
 * @package Tutor
 */

use TUTOR\Tutor;

defined( 'ABSPATH' ) || exit;

$tutor_autoloader = __DIR__ . '/vendor/autoload.php';

if ( is_readable( $tutor_autoloader ) ) {
	require_once $tutor_autoloader;
} else {
	/**
	 * Keep source-archive installs bootable when Composer's generated loader is
	 * absent. Production releases still include vendor/autoload.php; this small
	 * PSR-4 fallback covers Tutor's own namespaces and the integrated subsystem.
	 */
	spl_autoload_register(
		static function ( $class_name ) {
			$prefixes = array(
				'Tutor\\PaymentGateways\\'       => __DIR__ . '/ecommerce/PaymentGateways/',
				'Tutor\\Components\\'            => __DIR__ . '/components/',
				'Tutor\\Migrations\\'            => __DIR__ . '/migrations/',
				'Tutor\\Ecommerce\\'             => __DIR__ . '/ecommerce/',
				'Tutor\\Helpers\\'               => __DIR__ . '/helpers/',
				'Tutor\\Traits\\'                => __DIR__ . '/traits/',
				'Tutor\\Models\\'                => __DIR__ . '/models/',
				'Tutor\\Cache\\'                 => __DIR__ . '/cache/',
				'Tutor\\GDPR\\'                  => __DIR__ . '/GDPR/',
				'Kitmage\\Tutor\\Entitlements\\' => __DIR__ . '/kitmage/entitlements/',
				'TUTOR\\'                         => __DIR__ . '/classes/',
			);

			foreach ( $prefixes as $prefix => $directory ) {
				if ( 0 !== strpos( $class_name, $prefix ) ) {
					continue;
				}

				$file = $directory . str_replace( '\\', '/', substr( $class_name, strlen( $prefix ) ) ) . '.php';
				if ( is_readable( $file ) ) {
					require_once $file;
				}
				return;
			}
		}
	);
}

/**
 * Constants for tutor plugin.
 *
 * @since 1.0.0
 */
define( 'TUTOR_VERSION', '4.0.6' );
define( 'TUTOR_FILE', __FILE__ );
define( 'TUTOR_ENV', 'PROD' ); // DEV || PROD.
define( 'KITMAGE_TUTOR_BUILD_VERSION', '4.0.6-km.1' );

require_once __DIR__ . '/kitmage/entitlements/Bootstrap.php';
\Kitmage\Tutor\Entitlements\Bootstrap::register( TUTOR_FILE );

/**
 * Load text domain for translations.
 *
 * @since 1.0.0
 */
add_action( 'init', fn () => load_plugin_textdomain( 'tutor', false, basename( __DIR__ ) . '/languages' ) );

/**
 * Do some task during activation
 *
 * @since 1.5.2
 * @since 2.6.2 Uninstall hook registered
 */
register_activation_hook( TUTOR_FILE, array( Tutor::class, 'tutor_activate' ) );
register_deactivation_hook( TUTOR_FILE, array( Tutor::class, 'tutor_deactivation' ) );
register_uninstall_hook( TUTOR_FILE, array( Tutor::class, 'tutor_uninstall' ) );

if ( ! function_exists( 'tutor_lms' ) ) {
	/**
	 * Run main instance of the Tutor
	 *
	 * @since 1.2.0
	 *
	 * @return Tutor
	 */
	function tutor_lms() {
		return Tutor::get_instance();
	}
}

$GLOBALS['tutor'] = tutor_lms();
