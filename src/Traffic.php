<?php declare(strict_types = 1);

namespace JDR\Garrote;

use DateTimeImmutable;
use DateTimeInterface;

class Traffic
{
    /**
     * The actual amount of calls done during the request expiresAt.
     *
     * @var int
     */
    private $calls = 0;

    /**
     * The maximum amount of calls allowed during the request expiresAt.
     *
     * @var int
     */
    private $limit;

    /**
     * The duration of the request expiresAt.
     *
     * @var DateTimeInterface
     */
    private $expiresAt;

    public function __construct(int $limit, DateTimeInterface $expiresAt)
    {
        $this->limit = $limit;
        $this->expiresAt = $expiresAt;
    }

    public static function fromWire(Wire $wire)
    {
        $expirationDate = new DateTimeImmutable('+' . $wire->getWindow() . ' seconds');

        return new static($wire->getLimit(), $expirationDate);
    }

    public function incrementCalls(): void
    {
        $this->calls++;
    }

    public function isLimitExceeded(): bool
    {
        return $this->calls >= $this->limit;
    }

    public function callsRemaining(): int
    {
        return max($this->limit - $this->calls, 0);
    }

    public function expiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function expiresAtTimestamp(): int
    {
        return $this->expiresAt->getTimestamp();
    }

    public function hasExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable('now');
    }

    public function formatTimeRemaining(string $format): string
    {
        return $this->expiresAt->diff(new DateTimeImmutable('now'))->format($format);
    }
}
