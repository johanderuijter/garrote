<?php declare(strict_types = 1);

namespace JDR\Garrote\Tests;

use JDR\Garrote\Garrote;
use JDR\Garrote\IdentificationStrategy;
use JDR\Garrote\Traffic;
use JDR\Garrote\Wire;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\SimpleCache\CacheInterface as Cache;

class GarroteTest extends TestCase
{
    private $cache;
    private $garrote;
    private $identificationStrategy;
    private $request;
    private $wire;

    public function setUp()
    {
        $this->cache = $this->prophesize(Cache::class);
        $this->cache->get(Argument::type('string'))->willReturn(null);
        $this->cache->set(Argument::type('string'), Argument::type(Traffic::class))->will(function ($args) {
            $this->get(Argument::type('string'))->willReturn($args[1]);
        });

        $this->request = $this->prophesize(ServerRequest::class);

        $this->wire = $this->prophesize(Wire::class);
        $this->wire->getLimit()->willReturn(3);
        $this->wire->getWindow()->willReturn(1);

        $this->identificationStrategy = $this->prophesize(IdentificationStrategy::class);
        $this->identificationStrategy->identify($this->request, $this->wire)->willReturn(Argument::type('string'));

        $this->garrote = new Garrote($this->cache->reveal(), $this->identificationStrategy->reveal());
    }

    public function testGarroteCanBeInitialized()
    {
        $this->assertInstanceOf(
            Garrote::class,
            $this->garrote
        );
    }

    public function testGarroteCanRetrieveTraffic()
    {
        $request = $this->request->reveal();
        $wire = $this->wire->reveal();

        $this->assertInstanceOf(
            Traffic::class,
            $this->garrote->getTraffic($request, $wire)
        );
    }

    public function testGarroteCanConstrictTraffic()
    {
        $request = $this->request->reveal();
        $wire = $this->wire->reveal();

        $this->garrote->constrict($request, $wire);

        $this->assertEquals(
            2,
            $this->garrote->getTraffic($request, $wire)->callsRemaining()
        );
    }

    public function testGarroteWillBlockTrafficOnceLimitIsReached()
    {
        $request = $this->request->reveal();
        $wire = $this->wire->reveal();

        for ($callsRemaining = 2; $callsRemaining >= 0; $callsRemaining--) {
            $this->garrote->constrict($request, $wire);

            $this->assertEquals(
                $callsRemaining,
                $this->garrote->getTraffic($request, $wire)->callsRemaining()
            );
        }

        $this->assertTrue($this->garrote->isBlocked($request, $wire));
    }

    public function testGarroteWillUnblockTrafficOnceWindowHasPast()
    {
        $request = $this->request->reveal();
        $wire = $this->wire->reveal();

        for ($callsRemaining = 2; $callsRemaining >= 0; $callsRemaining--) {
            $this->garrote->constrict($request, $wire);
        }

        $this->assertTrue($this->garrote->isBlocked($request, $wire));

        // No idea how to mock this
        sleep(1);

        $this->assertFalse($this->garrote->isBlocked($request, $wire));
    }
}
