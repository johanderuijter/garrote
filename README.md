# Garrote
Rate Limiter for PHP.

Works with [PSR-7 HTTP message interfaces][link-psr-7] and any [PSR-16 SimpleCache implementation][link-psr-16].

## Usage

Basic usage:
```php
<?php
use JDR\Garrote\Garrote;
use JDR\Garrote\Wire;
use Zend\Diactoros\ServerRequest;

$garrote = new Garrote(new SimpleCache(), new SomeIdentificationStrategy());

$request = new ServerRequest();
$wire = new Wire('api', 10, 5);

if ($garrote->isBlocked($request, $wire)) {
    return;
}
$garrote->constrict($request, $wire);
```

This library comes with 2 IdentificationStrategies. The `RequestAttributeIdentificationStrategy` will use a given request attribute with the wire endpoint to compose an identifier.
```php
<?php
use JDR\Garrote\Garrote;
use JDR\Garrote\RequestAttributeIdentificationStrategy;
use JDR\Garrote\Wire;
use Zend\Diactoros\ServerRequest;

$garrote = new Garrote(new SimpleCache(), new RequestAttributeIdentificationStrategy('client_ip'));

$request = (new ServerRequest())->withAttribute('client_ip', '127.0.0.1');
// ...
```

You can also use the `CallbackIdentificationStrategy` to use any callable to determine the identifier.

```php
<?php
use JDR\Garrote\Garrote;
use JDR\Garrote\CallbackIdentificationStrategy;
use JDR\Garrote\Wire;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;

$garrote = new Garrote(new SimpleCache(), new CallbackIdentificationStrategy(function (ServerRequestInterface $request, Wire $wire) {
    return sprintf('%s-%s', $request->getAttribute('client_ip'), $wire->getEndpoint());
}));

$request = (new ServerRequest())->withAttribute('client_ip', '127.0.0.1');
// ...
```

Lastly, you can implement your own `IdentificationStrategy`.

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.

[link-psr-7]: http://www.php-fig.org/psr/psr-7/
[link-psr-15]: https://github.com/php-fig/fig-standards/tree/master/proposed/http-middleware
[link-psr-16]: http://www.php-fig.org/psr/psr-16/
