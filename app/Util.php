<?php
namespace App;

class Util {

    public static function encryptString($string, $key) {
        $encrypted = openssl_encrypt($string, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv = openssl_random_pseudo_bytes(16));
        $encoded = base64_encode($iv . $encrypted);
        return $encoded;
    }

    public static function decryptString($string, $key) {
        $decoded = base64_decode($string);
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);
        $decrypted = openssl_decrypt($encrypted, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    /**
     * 补齐加密秘钥到 32 位，为了方便客户端解密
     * @param $key
     * @return mixed|string
     */
    public static function  padKey($key) {
        while (strlen($key) < 32) {
            $key .= '0';
        }
        return $key;
    }


}
