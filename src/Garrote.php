<?php declare(strict_types = 1);

namespace JDR\Garrote;

use Psr\SimpleCache\CacheInterface as Cache;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class Garrote
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache, IdentificationStrategy $identificationStrategy)
    {
        $this->cache = $cache;
        $this->identificationStrategy = $identificationStrategy;
    }

    public function isBlocked(ServerRequest $request, Wire $wire): bool
    {
        $traffic = $this->getTraffic($request, $wire);

        return $traffic->isLimitExceeded();
    }

    public function constrict(ServerRequest $request, Wire $wire): void
    {
        $traffic = $this->getTraffic($request, $wire);

        $traffic->incrementCalls();

        $this->cache->set($this->identificationStrategy->identify($request, $wire), $traffic);
    }

    public function getTraffic(ServerRequest $request, Wire $wire): Traffic
    {
        $traffic = $this->cache->get($this->identificationStrategy->identify($request, $wire));

        if (!$traffic || $traffic->hasExpired()) {
            $traffic = Traffic::fromWire($wire);
        }

        return $traffic;
    }
}
