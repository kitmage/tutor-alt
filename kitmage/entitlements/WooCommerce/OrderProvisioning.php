<?php
namespace Kitmage\Tutor\Entitlements\WooCommerce;
use Kitmage\Tutor\Entitlements\Service\ProvisioningService;
final class OrderProvisioning {public static function register(){foreach(array('woocommerce_payment_complete','woocommerce_order_status_processing','woocommerce_order_status_completed') as $hook)add_action($hook,array(__CLASS__,'provision'),10,1);add_action('woocommerce_subscription_renewal_payment_complete',array(__CLASS__,'renewal'),10,2);}public static function provision($id){(new ProvisioningService())->provision_order($id);}public static function renewal($subscription,$order){$id=is_object($order)?$order->get_id():(int)$order;self::provision($id);}}
