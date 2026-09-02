<?php
$root=dirname(__DIR__,2);$fail=[];
$checks=['classes/Course.php'=>["PRICE_TYPE_ENTITLEMENT  = 'entitlement'",'is_course_entitlement_only'],'tutor.php'=>['Update URI: https://github.com/kitmage/tutor-alt','TUTOR_VERSION','TUTOR_FILE'],'assets/src/js/v3/entries/course-builder/services/course.ts'=>["'free' | 'paid' | 'entitlement'"],'kitmage/entitlements/Database/Migrator.php'=>['UNIQUE KEY source_item','UNIQUE KEY batch_user'],'kitmage/entitlements/Service/ReservationService.php'=>['entitlements_used+entitlements_reserved<entitlements_total','START TRANSACTION']];
foreach($checks as $file=>$needles){$text=file_get_contents("$root/$file");foreach($needles as $needle)if(false===strpos($text,$needle))$fail[]="$file missing $needle";}
if($fail){fwrite(STDERR,implode("\n",$fail)."\n");exit(1);}echo "PASS: Kitmage static regression contracts\n";
