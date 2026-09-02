<?php
namespace Kitmage\Tutor\Entitlements\Service;
use Tutor\Models\EnrollmentModel;
final class EnrollmentService {
	public function is_enrolled($course,$user){return (bool) EnrollmentModel::is_enrolled((int)$course,(int)$user);}
	public function enroll($course,$user){$id=EnrollmentModel::do_enroll((int)$course,0,(int)$user);return $id && $this->is_enrolled($course,$user);}
}
