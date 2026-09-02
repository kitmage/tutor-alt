<?php
namespace Kitmage\Tutor\Entitlements\Service;
final class ReservationService {
	public function reserve($batch,$user){
		global $wpdb;$bt=$wpdb->prefix.'kte_batches';$rt=$wpdb->prefix.'kte_redemptions';$now=current_time('mysql',true);
		$wpdb->query('START TRANSACTION');
		try {
			$changed=$wpdb->query($wpdb->prepare("UPDATE $bt SET entitlements_reserved=entitlements_reserved+1,updated_at=%s WHERE id=%d AND status='active' AND expires_at>%s AND entitlements_used+entitlements_reserved<entitlements_total",$now,$batch->id,$now));
			if(1!==$changed){$wpdb->query('ROLLBACK');return new \WP_Error('no_capacity',__('This invitation has no available seats.','tutor'));}
			$sql=$wpdb->prepare("INSERT INTO $rt (batch_id,user_id,course_id,status,reserved_at,created_at,updated_at) VALUES (%d,%d,%d,'pending',%s,%s,%s) ON DUPLICATE KEY UPDATE status=IF(status='failed','pending',status),reserved_at=IF(status='failed',VALUES(reserved_at),reserved_at),updated_at=VALUES(updated_at),id=LAST_INSERT_ID(id)",$batch->id,$user,$batch->course_id,$now,$now,$now);
			if(false===$wpdb->query($sql))throw new \RuntimeException('redemption_insert_failed');
			$id=(int)$wpdb->insert_id;$status=$wpdb->get_var($wpdb->prepare("SELECT status FROM $rt WHERE id=%d",$id));
			if('pending'!==$status){$wpdb->query($wpdb->prepare("UPDATE $bt SET entitlements_reserved=GREATEST(0,entitlements_reserved-1) WHERE id=%d",$batch->id));$wpdb->query('COMMIT');return new \WP_Error('duplicate',__('This invitation was already redeemed.','tutor'));}
			$wpdb->query('COMMIT');return $id;
		}catch(\Throwable $e){$wpdb->query('ROLLBACK');return new \WP_Error('reservation_failed',__('A seat could not be reserved.','tutor'));}
	}
	public function finalize($id,$batch,$user){global $wpdb;$u=get_userdata($user);$now=current_time('mysql',true);$wpdb->query('START TRANSACTION');$changed=$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}kte_redemptions SET status='completed',first_name_snapshot=%s,last_name_snapshot=%s,email_snapshot=%s,redeemed_at=%s,updated_at=%s WHERE id=%d AND status='pending'",get_user_meta($user,'first_name',true),get_user_meta($user,'last_name',true),$u?$u->user_email:'',$now,$now,$id));if(1===$changed){$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}kte_batches SET entitlements_reserved=entitlements_reserved-1,entitlements_used=entitlements_used+1,status=IF(entitlements_used+1>=entitlements_total,'exhausted','active'),updated_at=%s WHERE id=%d AND entitlements_reserved>0",$now,$batch));$wpdb->query('COMMIT');return true;}$wpdb->query('ROLLBACK');return false;}
	public function fail($id,$batch,$code,$context=''){global $wpdb;$now=current_time('mysql',true);$wpdb->query('START TRANSACTION');$changed=$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}kte_redemptions SET status='failed',failure_code=%s,failure_context=%s,updated_at=%s WHERE id=%d AND status='pending'",sanitize_key($code),sanitize_textarea_field($context),$now,$id));if(1===$changed)$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}kte_batches SET entitlements_reserved=GREATEST(0,entitlements_reserved-1),updated_at=%s WHERE id=%d",$now,$batch));$wpdb->query('COMMIT');return 1===$changed;}
}
