<?php declare(strict_types = 1);

namespace JDR\Garrote\Tests;

use JDR\Garrote\RequestAttributeIdentificationStrategy;
use JDR\Garrote\Wire;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class RequestAttributeIdentificationStrategyTest extends TestCase
{
    public function testIdentificationStrategyCanBeInitialized()
    {
        $this->assertInstanceOf(
            RequestAttributeIdentificationStrategy::class,
            new RequestAttributeIdentificationStrategy('attribute')
        );
    }

    public function testIdentificationStrategyUsesRequestAttributeAsIdentity()
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getAttribute(Argument::exact('attribute'))->willReturn('attribute');

        $wire = $this->prophesize(Wire::class);
        $wire->getEndpoint()->willReturn('endpoint');

        $strategy = new RequestAttributeIdentificationStrategy('attribute');

        $this->assertEquals(
            'attribute-endpoint',
            $strategy->identify($request->reveal(), $wire->reveal())
        );
    }
}
