<?php
namespace Kitmage\Tutor\Entitlements\Service;
final class ReconciliationService {
	const CRON_HOOK='kitmage_training_entitlements/reconcile';
	public static function register(){add_action(self::CRON_HOOK,array(__CLASS__,'run'));if(!wp_next_scheduled(self::CRON_HOOK))wp_schedule_event(time()+300,'hourly',self::CRON_HOOK);}
	public static function run(){global $wpdb;$cutoff=gmdate('Y-m-d H:i:s',time()-15*MINUTE_IN_SECONDS);$rows=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}kte_redemptions WHERE status='pending' AND reserved_at<%s LIMIT 100",$cutoff));$e=new EnrollmentService();$r=new ReservationService();foreach($rows as $row){if($e->is_enrolled($row->course_id,$row->user_id))$r->finalize($row->id,$row->batch_id,$row->user_id);else $r->fail($row->id,$row->batch_id,'stale_reservation');}}
}
