<?php

namespace Wsdl2PhpGenerator\Tests\Functional;

/**
 * Test handling of abstract types and extensions.
 */
class RecursiveReferencesTest extends Wsdl2PhpGeneratorFunctionalTestCase
{

    protected function getWsdlPath()
    {
        return $this->fixtureDir . '/recursivereferences/abstract.wsdl';
    }

    public function testAbstract()
    {
        $this->assertTrue(true);
    }
}
