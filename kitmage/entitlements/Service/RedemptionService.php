<?php
namespace Kitmage\Tutor\Entitlements\Service;
use Kitmage\Tutor\Entitlements\Repository\BatchRepository;
use Kitmage\Tutor\Entitlements\Repository\RedemptionRepository;
final class RedemptionService {
	public function redeem($token,$user){$b=(new BatchRepository())->by_token($token);if(!$b)return new \WP_Error('invalid_token',__('This invitation is invalid.','tutor'));if('active'!==$b->status)return new \WP_Error('inactive',__('This invitation is not active.','tutor'));if(strtotime($b->expires_at)<=current_time('timestamp',true))return new \WP_Error('expired',__('This invitation has expired.','tutor'));if('courses'!==get_post_type($b->course_id)||!tutor_utils()->is_course_entitlement_only($b->course_id))return new \WP_Error('course_unavailable',__('The invited course is unavailable.','tutor'));
		$rr=new RedemptionRepository();if($rr->completed($b->id,$user))return array('status'=>'already_redeemed','course_id'=>(int)$b->course_id);$enroll=new EnrollmentService();if($enroll->is_enrolled($b->course_id,$user))return array('status'=>'already_enrolled','course_id'=>(int)$b->course_id);
		$res=new ReservationService();$id=$res->reserve($b,$user);if(is_wp_error($id))return $id;if($enroll->enroll($b->course_id,$user)&&$res->finalize($id,$b->id,$user)){do_action('kitmage_training_entitlements/redemption_completed',$id,(int)$b->id,(int)$user);return array('status'=>'completed','course_id'=>(int)$b->course_id);}$res->fail($id,$b->id,'tutor_enrollment_failed');do_action('kitmage_training_entitlements/redemption_failed',$id,(int)$b->id,'tutor_enrollment_failed');return new \WP_Error('enrollment_failed',__('Enrollment could not be completed. Please try again.','tutor'));}
}
