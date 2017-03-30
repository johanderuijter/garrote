<?php declare(strict_types = 1);

namespace JDR\Garrote;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class CallbackIdentificationStrategy implements IdentificationStrategy
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function identify(ServerRequest $request, Wire $wire): string
    {
        return (string) ($this->callback)($request, $wire);
    }
}
