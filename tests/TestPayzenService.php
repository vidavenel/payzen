<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 16:40
 */

namespace Vidavenel\Payzen\Tests;

use Orchestra\Testbench\TestCase;
use Vidavenel\Payzen\PayzenService;
use Vidavenel\Payzen\PayzenServiceProvider;

class TestPayzenService extends TestCase
{
    public function testExemple()
    {
        $this->assertTrue(true);
    }

    protected function getPackageProviders($app)
    {
        return [
            PayzenServiceProvider::class
        ];
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testCheckAdresse()
    {
        $true_address = [
            'adresse' => '3 rue des roses',
            'cp' => '75 000',
            'ville' => 'Paris'
        ];
        $false_address = [
            'adresse' => '3 rue des roses',
            'cp' => '75 000'
        ];
        $service = resolve(PayzenService::class);

        $this->assertTrue($service->checkAdresse($true_address));
        $this->assertFalse($service->checkAdresse($false_address));
    }

    public function testCalculSignature()
    {
        $service = resolve(PayzenService::class);
        $array = [
            'vads_action_mode' => 'INTERACTIVE',
            'vads_ctx_mode' => 'TEST',
            'vads_page_action' => 'PAYMENT',
            'vads_site_id' => 123,
            'vads_version' => 'V2'
        ];
        $code = SHA1('INTERACTIVETESTPAYMENT123V2');
        $result = $this->invokeMethod($service, 'calculSignature', [$array]);
        // $this->assertEquals($code, $service->calculSignature($array));
        $this->assertEquals($code, $result);
    }
}