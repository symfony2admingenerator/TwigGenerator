<?php
namespace TwigGenerator\Tests\Extension;

use TwigGenerator\Extension\PHPPrintExtension;

/**
 * Test PHPPrintExtension class
 */
class PHPPrintExtensionTest extends BaseExtensionTest
{
    /**
     * @return array
     */
    protected function getTwigVariables()
    {
        return array();
    }

    /**
     * @return PHPPrintExtension
     */
    protected function getTestedExtension()
    {
        return new PHPPrintExtension();
    }

    public function testPhpName()
    {
        $tpls = array(
            'string' => '{{ "cedric-is-valid"|php_name }}',
        );

        $returns = array(
            'string' => array("cedricisvalid", 'Php name format well the string'),
        );

        $this->runTwigTests($tpls, $returns);
    }
}