<?php declare(strict_types = 1);

namespace JDR\Garrote\Tests;

use JDR\Garrote\Wire;
use PHPUnit\Framework\TestCase;

class WireTest extends TestCase
{
    public function testWireCanBeInitialized()
    {
        $this->assertInstanceOf(
            Wire::class,
            new Wire('endpoint', 10, 15)
        );
    }

    public function testEndpointCanBeRetrieved()
    {
        $wire = new Wire('endpoint', 10, 15);

        $this->assertEquals(
            'endpoint',
            $wire->getEndpoint()
        );
    }

    public function testLimitCanBeRetrieved()
    {
        $wire = new Wire('endpoint', 10, 15);

        $this->assertEquals(
            10,
            $wire->getLimit()
        );
    }

    public function testWindowCanBeRetrieved()
    {
        $wire = new Wire('endpoint', 10, 15);

        $this->assertEquals(
            15,
            $wire->getWindow()
        );
    }
}
