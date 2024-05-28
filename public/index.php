<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Booking;
use App\BookingController;

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$container->set(Twig::class, function() {
    return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
});

$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

(require __DIR__ . '/../config/routes.php')($app);

$app->run();
