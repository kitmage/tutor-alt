<?php
namespace Kitmage\Tutor\Entitlements\Service;
final class TokenService {
	public static function generate() { return rtrim( strtr( base64_encode( random_bytes( 32 ) ), '+/', '-_' ), '=' ); }
	public static function hash( $token ) { return hash_hmac( 'sha256', (string) $token, wp_salt( 'auth' ) ); }
	public static function encrypt( $token ) { $key=hash('sha256',wp_salt('secure_auth'),true);$iv=random_bytes(12);$tag='';$cipher=openssl_encrypt($token,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tag);return base64_encode($iv.$tag.$cipher); }
	public static function decrypt( $value ) { $raw=base64_decode((string)$value,true);if(false===$raw||strlen($raw)<29)return ''; $key=hash('sha256',wp_salt('secure_auth'),true);return (string)openssl_decrypt(substr($raw,28),'aes-256-gcm',$key,OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16)); }
	public static function valid_format( $token ) { return 1 === preg_match( '/^[A-Za-z0-9_-]{43}$/D', (string) $token ); }
}
