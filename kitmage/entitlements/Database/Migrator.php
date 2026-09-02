<?php
namespace Kitmage\Tutor\Entitlements\Database;
final class Migrator {
	const VERSION = '1.0.0';
	public static function migrate() {
		global $wpdb; require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$c = $wpdb->get_charset_collate();
		dbDelta( "CREATE TABLE {$wpdb->prefix}kte_batches (
		id bigint unsigned NOT NULL AUTO_INCREMENT, token_hash char(64) NOT NULL, token_cipher text NOT NULL, token_version int unsigned NOT NULL DEFAULT 1,
		customer_user_id bigint unsigned NOT NULL, order_id bigint unsigned NOT NULL, order_item_id bigint unsigned NOT NULL,
		subscription_id bigint unsigned NOT NULL DEFAULT 0, product_id bigint unsigned NOT NULL, variation_id bigint unsigned NOT NULL DEFAULT 0,
		course_id bigint unsigned NOT NULL, entitlements_total int unsigned NOT NULL, entitlements_used int unsigned NOT NULL DEFAULT 0,
		entitlements_reserved int unsigned NOT NULL DEFAULT 0, created_at datetime NOT NULL, expires_at datetime NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'active', created_by varchar(40) NOT NULL, updated_at datetime NOT NULL,
		PRIMARY KEY (id), UNIQUE KEY source_item (order_id,order_item_id), UNIQUE KEY token_hash (token_hash),
		KEY customer (customer_user_id), KEY subscription (subscription_id), KEY course (course_id), KEY status (status), KEY expires (expires_at)
		) $c;" );
		dbDelta( "CREATE TABLE {$wpdb->prefix}kte_redemptions (
		id bigint unsigned NOT NULL AUTO_INCREMENT, batch_id bigint unsigned NOT NULL, user_id bigint unsigned NOT NULL, course_id bigint unsigned NOT NULL,
		first_name_snapshot varchar(200) NOT NULL DEFAULT '', last_name_snapshot varchar(200) NOT NULL DEFAULT '', email_snapshot varchar(320) NOT NULL DEFAULT '',
		status varchar(20) NOT NULL, reserved_at datetime NOT NULL, redeemed_at datetime NULL, failure_code varchar(80) NOT NULL DEFAULT '', failure_context text NULL,
		created_at datetime NOT NULL, updated_at datetime NOT NULL, PRIMARY KEY (id), UNIQUE KEY batch_user (batch_id,user_id), KEY stale (status,reserved_at), KEY course (course_id)
		) $c;" );
		dbDelta( "CREATE TABLE {$wpdb->prefix}kte_audit_log (
		id bigint unsigned NOT NULL AUTO_INCREMENT, batch_id bigint unsigned NOT NULL, action varchar(80) NOT NULL,
		previous_value longtext NULL, new_value longtext NULL, actor_user_id bigint unsigned NOT NULL DEFAULT 0, context longtext NULL, created_at datetime NOT NULL,
		PRIMARY KEY (id), KEY batch (batch_id), KEY action (action), KEY created (created_at)
		) $c;" );
		update_option( 'kte_db_version', self::VERSION, false );
	}
}
