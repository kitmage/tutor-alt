<?php
namespace Kitmage\Tutor\Entitlements\Repository;
final class RedemptionRepository {
	public function completed($batch,$user){global $wpdb;return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}kte_redemptions WHERE batch_id=%d AND user_id=%d AND status='completed'",$batch,$user));}
	public function roster($batch){global $wpdb;return $wpdb->get_results($wpdb->prepare("SELECT first_name_snapshot,last_name_snapshot,email_snapshot,redeemed_at FROM {$wpdb->prefix}kte_redemptions WHERE batch_id=%d AND status='completed' ORDER BY redeemed_at",$batch));}
}
