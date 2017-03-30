<?php declare(strict_types = 1);

namespace JDR\Garrote;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;

interface IdentificationStrategy
{
    public function identify(ServerRequest $request, Wire $wire): string;
}
