<?php

namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use DI\Container;
use App\Controller\BookingController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class BookingIntegrationTest extends TestCase
{
    protected $app;
    protected $pdo;

    protected function setUp(): void
    {
        $container = new Container();
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        $container->set(Twig::class, function () {
            return Twig::create(__DIR__ . '/../../templates', ['cache' => false]);
        });

        $this->pdo = new \PDO('sqlite:' . __DIR__ . '/../../var/database.test.sqlite');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $container->set(\PDO::class, function () {
            return $this->pdo;
        });

        $this->setupDatabase();

        (require __DIR__ . '/../../config/routes.php')($this->app);
    }

    protected function setupDatabase(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS spectacles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            capacity INTEGER NOT NULL
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            spectacle_id INTEGER NOT NULL,
            seatNumber INTEGER NOT NULL,
            rang TEXT NOT NULL,
            FOREIGN KEY (spectacle_id) REFERENCES spectacles (id)
        )");

        $spectacles = [
            ['name' => 'Spectacle 1', 'description' => 'Description du Spectacle 1', 'capacity' => 127],
            ['name' => 'Spectacle 2', 'description' => 'Description du Spectacle 2', 'capacity' => 163],
            ['name' => 'Spectacle 3', 'description' => 'Description du Spectacle 3', 'capacity' => 197],
        ];

        $stmt = $this->pdo->prepare("INSERT INTO spectacles (name, description, capacity) 
        VALUES (:name, :description, :capacity)");

        foreach ($spectacles as $spectacle) {
            $stmt->execute([
                ':name' => $spectacle['name'],
                ':description' => $spectacle['description'],
                ':capacity' => $spectacle['capacity'],
            ]);
        }
    }

    public function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM bookings");
        $this->pdo->exec("DELETE FROM spectacles");
    }

    public function testFullBookingFlow()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/booking');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/booking')
            ->withParsedBody([
                'spectacle_id' => 1,
                'seatNumber' => 1,
                'rang' => 'A'
            ]);

        $response = $this->app->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
    }
}
