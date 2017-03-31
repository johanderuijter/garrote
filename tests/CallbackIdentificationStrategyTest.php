<?php declare(strict_types = 1);

namespace JDR\Garrote\Tests;

use JDR\Garrote\CallbackIdentificationStrategy;
use JDR\Garrote\Wire;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class CallbackIdentificationStrategyTest extends TestCase
{
    public function testIdentificationStrategyCanBeInitialized()
    {
        $this->assertInstanceOf(
            CallbackIdentificationStrategy::class,
            new CallbackIdentificationStrategy(function () {
                return 'string';
            })
        );
    }

    public function testIdentificationStrategyUsesRequestAttributeAsIdentity()
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getAttribute(Argument::exact('attribute'))->willReturn('attribute');

        $wire = $this->prophesize(Wire::class);
        $wire->getEndpoint()->willReturn('endpoint');

        $strategy = new CallbackIdentificationStrategy(function (ServerRequest $request, Wire $wire) {
            return sprintf('%s-%s', $request->getAttribute('attribute'), $wire->getEndpoint());
        });

        $this->assertEquals(
            'attribute-endpoint',
            $strategy->identify($request->reveal(), $wire->reveal())
        );
    }
}
