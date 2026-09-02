<?php
namespace Kitmage\Tutor\Entitlements;

use Kitmage\Tutor\Entitlements\Admin\Menu;
use Kitmage\Tutor\Entitlements\Database\Migrator;
use Kitmage\Tutor\Entitlements\Frontend\RedemptionController;
use Kitmage\Tutor\Entitlements\Service\ReconciliationService;
use Kitmage\Tutor\Entitlements\WooCommerce\MyAccount;
use Kitmage\Tutor\Entitlements\WooCommerce\OrderProvisioning;
use Kitmage\Tutor\Entitlements\WooCommerce\ProductSettings;
use Kitmage\Tutor\Entitlements\WooCommerce\RefundHandler;

final class Bootstrap {
	private static $file;
	public static function register( $file ) {
		self::$file = $file;
		spl_autoload_register( static function ( $class ) {
			$prefix = __NAMESPACE__ . '\\';
			if ( 0 !== strpos( $class, $prefix ) ) return;
			$file = __DIR__ . '/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( is_readable( $file ) ) require_once $file;
		} );
		register_activation_hook( $file, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( $file, array( __CLASS__, 'deactivate' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'boot' ), 20 );
		add_action( 'before_woocommerce_init', static function () {
			if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', self::$file, true );
			}
		} );
	}
	public static function activate() { Migrator::migrate(); RedemptionController::add_rewrite_rule(); flush_rewrite_rules(); }
	public static function deactivate() { wp_clear_scheduled_hook( ReconciliationService::CRON_HOOK ); flush_rewrite_rules(); }
	public static function boot() {
		RedemptionController::register();
		Menu::register();
		ReconciliationService::register();
		if ( class_exists( 'WooCommerce' ) ) {
			ProductSettings::register(); OrderProvisioning::register(); RefundHandler::register(); MyAccount::register();
		} elseif ( is_admin() ) {
			add_action( 'admin_notices', static function () { echo '<div class="notice notice-warning"><p>' . esc_html__( 'Kitmage Training Entitlements purchasing requires WooCommerce.', 'tutor' ) . '</p></div>'; } );
		}
	}
}
