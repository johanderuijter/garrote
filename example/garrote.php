<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use JDR\Garrote\Garrote;
use JDR\Garrote\RequestAttributeIdentificationStrategy;
use JDR\Garrote\Wire;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Zend\Diactoros\ServerRequest;

$request = (new ServerRequest())->withAttribute('client_ip', '127.0.0.1');

$cache = new FilesystemCache('jdr.garrote');
$garrote = new Garrote($cache, new RequestAttributeIdentificationStrategy('client_ip'));

$wire = new Wire('api', 10, 5);

while (!$blocked = $garrote->isBlocked($request, $wire)) {
    $garrote->constrict($request, $wire);

    $traffic = $garrote->getTraffic($request, $wire);
    echo "Calls remaining: {$traffic->callsRemaining()}".PHP_EOL;
}

while ($blocked = $garrote->isBlocked($request, $wire)) {
    $traffic = $garrote->getTraffic($request, $wire);
    echo "Request blocked. Seconds remaining: {$traffic->formatTimeRemaining('%s')}".PHP_EOL;
    sleep(1);
}

$traffic = $garrote->getTraffic($request, $wire);
echo "Calls remaining: {$traffic->callsRemaining()}".PHP_EOL;
