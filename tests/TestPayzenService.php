<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 16:40
 */

namespace Vidavenel\Payzen\Tests;

use Orchestra\Testbench\TestCase;
use Vidavenel\Payzen\PayzenServiceProvider;

class TestPayzenService extends TestCase
{
    public function testExemple()
    {
        $this->assertTrue(true);
    }

    public function testForm()
    {
        $service = resolve('payzen');
        $this->assertTrue(true);
    }

    protected function getPackageProviders($app)
    {
        return [
            PayzenServiceProvider::class
        ];
    }
}