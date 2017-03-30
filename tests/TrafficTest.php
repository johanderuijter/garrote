<?php declare(strict_types = 1);

namespace JDR\Garrote\Tests;

use DateTimeImmutable;
use JDR\Garrote\Traffic;
use JDR\Garrote\Wire;
use PHPUnit\Framework\TestCase;

class TrafficTest extends TestCase
{
    private $traffic;

    public function setUp()
    {
        $this->traffic = new Traffic(3, new DateTimeImmutable('+15 seconds'));
    }

    public function testTrafficCanBeInitialized()
    {
        $this->assertInstanceOf(
            Traffic::class,
            $this->traffic
        );
    }

    public function testTrafficCanBeInitializedFromWire()
    {
        $wire = $this->prophesize(Wire::class);
        $wire->getLimit()->willReturn(3);
        $wire->getWindow()->willReturn(15);

        $this->assertEquals(
            $this->traffic,
            Traffic::fromWire($wire->reveal()),
            null,
            1
        );
    }

    public function testTrafficCanRetrieveRemainingCalls()
    {
        $this->traffic->incrementCalls();

        $this->assertEquals(
            $this->traffic->callsRemaining(),
            2
        );
    }

    public function testRemainingCallsIsNeverLessThanZero()
    {
        for ($callsRemaining = 2; $callsRemaining >= 0; $callsRemaining--) {
            $this->traffic->incrementCalls();

            $this->assertEquals(
                $callsRemaining,
                $this->traffic->callsRemaining()
            );
        }

        for ($callsRemaining = 0; $callsRemaining >= -2; $callsRemaining--) {
            $this->traffic->incrementCalls();

            $this->assertEquals(
                0,
                $this->traffic->callsRemaining()
            );
        }
    }

    public function testTrafficKnowsWhenLimitIsExceeded()
    {
        for ($callsRemaining = 3; $callsRemaining > 0; $callsRemaining--) {
            $this->assertFalse($this->traffic->isLimitExceeded());
            $this->traffic->incrementCalls();
        }

        $this->assertTrue($this->traffic->isLimitExceeded());
    }

    public function testTrafficExpirationDate()
    {
        $traffic = new Traffic(3, new DateTimeImmutable('-15 seconds'));

        $this->assertTrue($traffic->hasExpired());
    }
}
