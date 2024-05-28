<?php

namespace Test\Unitaire;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Twig\Loader\FilesystemLoader;

class HomePageTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $container = new Container();
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        $container->set(Twig::class, function () {
            $loader = new FilesystemLoader(__DIR__ . '/../../templates');
            return new Twig($loader, ['cache' => false]);
        });

        $this->app->add(TwigMiddleware::createFromContainer($this->app, Twig::class));

        (require __DIR__ . '/../../config/routes.php')($this->app);
    }

    public function testHomePage()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $response = (new ResponseFactory())->createResponse();

        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
