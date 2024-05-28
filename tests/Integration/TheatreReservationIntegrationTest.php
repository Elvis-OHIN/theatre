<?php

namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class TheatreReservationIntegrationTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $container = new Container();
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        $container->set(Twig::class, function () {
            return Twig::create(__DIR__ . '/../../templates', ['cache' => false]);
        });

        $this->app->add(TwigMiddleware::createFromContainer($this->app, Twig::class));

        (require __DIR__ . '/../../config/routes.php')($this->app);
    }

    public function testHomePage()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
