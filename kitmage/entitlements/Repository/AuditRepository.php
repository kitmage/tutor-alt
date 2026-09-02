<?php
namespace Kitmage\Tutor\Entitlements\Repository;
final class AuditRepository {
	public function add( $batch_id, $action, $before = null, $after = null, array $context = array(), $actor = null ) {
		global $wpdb; return $wpdb->insert( $wpdb->prefix . 'kte_audit_log', array( 'batch_id'=>(int)$batch_id, 'action'=>sanitize_key($action), 'previous_value'=>wp_json_encode($before), 'new_value'=>wp_json_encode($after), 'actor_user_id'=>null === $actor ? get_current_user_id() : (int)$actor, 'context'=>wp_json_encode($context), 'created_at'=>current_time('mysql', true) ) );
	}
}
