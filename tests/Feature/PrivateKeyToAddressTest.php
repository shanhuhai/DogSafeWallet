<?php

namespace Tests\Feature;

use App\Helper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrivateKeyToAddressTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $address = Helper::getAddressFromPrivateKey('c7f5db0a7591e07ab169591f43f7d56c595df2bce785e9616a3b72167168f214');
        $this->assertEqualsIgnoringCase($address, '0x097466eFF893CD33B9569906e9D43310e406EF5B');
    }
}
