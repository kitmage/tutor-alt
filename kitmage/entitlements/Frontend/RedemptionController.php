<?php
namespace Kitmage\Tutor\Entitlements\Frontend;
use Kitmage\Tutor\Entitlements\Repository\BatchRepository;
use Kitmage\Tutor\Entitlements\Service\RedemptionService;
final class RedemptionController {
	public static function register(){add_action('init',array(__CLASS__,'add_rewrite_rule'));add_filter('query_vars',static function($v){$v[]='kte_token';return $v;});add_action('template_redirect',array(__CLASS__,'dispatch'));}
	public static function add_rewrite_rule(){add_rewrite_rule('^training/redeem/([A-Za-z0-9_-]{43})/?$','index.php?kte_token=$matches[1]','top');}
	public static function dispatch(){ $token=get_query_var('kte_token');if(!$token)return;$batch=(new BatchRepository())->by_token($token);status_header($batch?200:404);nocache_headers();$result=null;if($batch&&is_user_logged_in()&&'POST'===$_SERVER['REQUEST_METHOD']){check_admin_referer('kte_redeem_'.$batch->id);$result=(new RedemptionService())->redeem($token,get_current_user_id());}include dirname(__DIR__).'/templates/redeem.php';exit;}
}
