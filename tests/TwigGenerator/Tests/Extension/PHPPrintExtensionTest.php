<?php
namespace TwigGenerator\Tests\Extension;

use TwigGenerator\Extension\PHPPrintExtension;

/**
 * Test PHPPrintExtension class
 */
class PHPPrintExtensionTest extends BaseExtensionTest
{
    protected function getTwigVariables(): array
    {
        return [];
    }

    protected function getTestedExtension(): PHPPrintExtension
    {
        return new PHPPrintExtension();
    }

    public function testPhpName(): void
    {
        $tpls = [
            'string' => '{{ "cedric-is-valid"|php_name }}',
        ];

        $returns = [
            'string' => ["cedricisvalid", 'Php name format well the string'],
        ];

        $this->runTwigTests($tpls, $returns);
    }
}