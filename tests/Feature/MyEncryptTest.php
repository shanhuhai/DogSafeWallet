<?php

namespace Tests\Feature;

use App\Util;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyEncryptTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $key = '123123';
        $key = Util::padKey($key);
        $string = 'n3dda8eec242b53de76c36f5fb1d8e40b787744f3dcfcc6c9ce9eace4d04de5c';
        $encryptedString = Util::encryptString($string, $key);
        echo "Encrypted string: $encryptedString\n";
        $decryptedString = Util::decryptString($encryptedString, $key);
        echo "Decrypted string: $decryptedString";
        $this->assertEquals($string, $decryptedString);

    }


}
