<?php

namespace Wsdl2PhpGenerator\Tests\Functional;

/**
 * I am not aware of anything in particular which is tested here. It is included for completeness.
 */
class AnyTest extends Wsdl2PhpGeneratorFunctionalTestCase {

    protected function getWsdlPath()
    {
        return $this->fixtureDir . '/any/any.wsdl';
    }

    public function testAny()
    {
        $this->assertGeneratedClassExists('Any');
    }
}
