<?php declare(strict_types = 1);

namespace JDR\Garrote;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class RequestAttributeIdentificationStrategy implements IdentificationStrategy
{
    /**
     * @var string
     */
    private $attribute;

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public function identify(ServerRequest $request, Wire $wire): string
    {
        return sprintf('%s-%s', $request->getAttribute($this->attribute), $wire->getEndpoint());
    }
}
