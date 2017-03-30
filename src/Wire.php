<?php declare(strict_types = 1);

namespace JDR\Garrote;

class Wire
{
    /**
     * An identifier for the endpoint that were the rate limit is applied.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The maximum amount of calls allowed during the request window.
     *
     * @var int
     */
    private $limit;

    /**
     * The length, in seconds, of the request window.
     *
     * @var int
     */
    private $window;

    public function __construct(string $endpoint, int $limit, int $window)
    {
        $this->endpoint = $endpoint;
        $this->limit = $limit;
        $this->window = $window;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getWindow(): int
    {
        return $this->window;
    }
}
