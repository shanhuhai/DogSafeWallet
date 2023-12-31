<?php
namespace App;
use Encore\Admin\Facades\Admin;
use Web3p\EthereumUtil\Util;

class Helper {

    public static function maskString($string, $starNum=3, $left=6, $right = 4)
    {
        $length = strlen($string);
        if ($left==0 && $right==0) {
            return str_repeat('*', $starNum);
        }
        return substr($string, 0, $left) . str_repeat('*', $starNum) . substr($string, -$right);
    }

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


    /**
     *  将私钥转为以太坊地址
     * @param $privateKey
     * @return mixed
     */
    public static function getAddressFromPrivateKey($privateKey)
    {
        // 创建 EthereumUtil 实例
        $util = new Util();

        // 使用私钥获取钱包地址
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        $address = $util->publicKeyToAddress($publicKey);

        return $address;
    }


    /** 获取当前用户的个人设置
     * @param $key
     * @param $default
     * @return int|mixed
     */
    public static function config($key, $default = 1){
        $configs  = Admin::user()->configs->pluck('value','key');
        return $configs->has($key) ?  $configs[$key] : $default;
    }


    public static function groups($userId  = null) {
        if (is_null($userId)) {
            $userId = Admin::user()->id;
        }

        $groups = Group::where('user_id', $userId)->get();

        return $groups->pluck('name', 'id');
    }
}
