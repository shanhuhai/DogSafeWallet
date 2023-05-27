<?php

namespace Tests\Feature;

use App\Wallet;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EncryptTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        // 正常的测试
        $private_key = 'a3dda8eec242b53de76c36f5fb1d8e40b787744f3dcfcc6c9ce9eace4d04de5a';
        $crypt = new Encrypter('bitcoin123111111', 'AES-128-CBC');
        $crypted_string = $crypt->encryptString($private_key);
       // var_dump($crypted_string);
        $crypted_string = 'eyJpdiI6IkdJSjEwM293c05LeEZsbFlySFlGaEE9PSIsInZhbHVlIjoiOG9HSXFvMUZkYU1NSitSRTBWSmowZ015b1wvc2NWM29uMFNWWElpNWU4enZCZGZMNHBKSTJpQWZjXC9lblNjc1BNd2s3S1wvVUtTbTlEZnB6RFNjclVQU2kxWW04UUhsQzNNMW5VNkVmQUZlYlU9IiwibWFjIjoiYzNmY2Y0MTE2NmZhMzNlNzU3MGMyMDhmNTJmNmZlN2E4OThlMjVmODBmOWI2YTg1Y2MyODVmZTUxZGE4NDdiNyJ9';
        $decrypted_string = $crypt->decryptString($crypted_string);
       // var_dump($decrypted_string) ;
        $this->assertEquals($private_key, $decrypted_string);

        // 用空白进行加密
//        $crypt2 = new Encrypter('', 'AES-256-CBC');
//        $crypted_string = $crypt2->encryptString($private_key);
//        $this->assertIsString($crypted_string);
//        $decrypted_string = $crypt2->decryptString($crypted_string);
//        $this->assertEquals($private_key, $decrypted_string);

        //写入一个钱包

//        $wallet = new Wallet();
//        $wallet->group_id = 1;
//        $wallet->address = 'xxxxx';
//        $wallet->private_key  = Crypt::encryptString($private_key, env('ENCRYPTION_KEY'));
//        $wallet->save();
    }
}
