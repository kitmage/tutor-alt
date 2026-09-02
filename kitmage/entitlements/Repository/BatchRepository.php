<?php
namespace Kitmage\Tutor\Entitlements\Repository;
use Kitmage\Tutor\Entitlements\Service\TokenService;
final class BatchRepository {
	public function create( array $d ) {
		global $wpdb; $now=current_time('mysql',true); $token=TokenService::generate();
		$row=array('token_hash'=>TokenService::hash($token),'token_cipher'=>TokenService::encrypt($token),'token_version'=>1,'customer_user_id'=>(int)$d['customer_user_id'],'order_id'=>(int)$d['order_id'],'order_item_id'=>(int)$d['order_item_id'],'subscription_id'=>(int)($d['subscription_id']??0),'product_id'=>(int)$d['product_id'],'variation_id'=>(int)($d['variation_id']??0),'course_id'=>(int)$d['course_id'],'entitlements_total'=>(int)$d['entitlements_total'],'entitlements_used'=>0,'entitlements_reserved'=>0,'created_at'=>$now,'expires_at'=>$d['expires_at'],'status'=>'active','created_by'=>sanitize_key($d['created_by']??'woocommerce'),'updated_at'=>$now);
		if ( false === $wpdb->insert($wpdb->prefix.'kte_batches',$row) ) return null;
		$id=(int)$wpdb->insert_id; (new AuditRepository())->add($id,'creation',null,$row,array(),0); return array('id'=>$id,'token'=>$token);
	}
	public function by_token($token){ global $wpdb; if(!TokenService::valid_format($token))return null; return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}kte_batches WHERE token_hash=%s",TokenService::hash($token))); }
	public function get($id){global $wpdb;return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}kte_batches WHERE id=%d",$id));}
	public function for_customer($uid,$limit=50,$offset=0){global $wpdb;return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}kte_batches WHERE customer_user_id=%d ORDER BY id DESC LIMIT %d OFFSET %d",$uid,$limit,$offset));}
	public function regenerate_token($id){global $wpdb;$token=TokenService::generate();$batch=$this->get($id);if(!$batch)return false;$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}kte_batches SET token_hash=%s,token_cipher=%s,token_version=token_version+1,updated_at=%s WHERE id=%d",TokenService::hash($token),TokenService::encrypt($token),current_time('mysql',true),$id));(new AuditRepository())->add($id,'token_regeneration',$batch->token_version,$batch->token_version+1);return $token;}
}
