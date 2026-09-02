<?php
namespace Kitmage\Tutor\Entitlements\WooCommerce;
use Kitmage\Tutor\Entitlements\Repository\AuditRepository;
final class RefundHandler {public static function register(){add_action('woocommerce_order_fully_refunded',array(__CLASS__,'refund'),10,2);}public static function refund($order_id,$refund_id){global $wpdb;$ids=$wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}kte_batches WHERE order_id=%d AND status NOT IN ('refunded','revoked')",$order_id));foreach($ids as $id){$wpdb->update($wpdb->prefix.'kte_batches',array('status'=>'refunded','updated_at'=>current_time('mysql',true)),array('id'=>$id));(new AuditRepository())->add($id,'refund_disablement',null,'refunded',array('refund_id'=>(int)$refund_id),0);}}}
