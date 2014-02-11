<?php

namespace Wsdl2PhpGenerator\Tests\Functional;

/**
 * This
 */
class AnonymousTest extends Wsdl2PhpGeneratorFunctionalTestCase {

    protected function getWsdlPath()
    {
        return $this->fixtureDir . '/anonymous/lp.wsdl';
    }

    public function testAnonymous()
    {
        $this->assertGeneratedClassExists('Lp_service');
        $headerClass = new \ReflectionClass('Header');
        $this->assertContains('Header1', $headerClass->getProperties());
    }
}
