<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;


$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$container->set(Twig::class, function() {
    return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
});

$container->set(PDO::class, function(ContainerInterface $c) {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../var/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

(require __DIR__ . '/../config/routes.php')($app);

$app->run();
