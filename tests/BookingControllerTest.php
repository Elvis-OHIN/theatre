<?php

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use App\Booking;
use App\BookingController;

class BookingControllerTest extends TestCase
{
    private $app;
    private $booking;

    public function setUp(): void
    {
        // Créer l'application Slim
        $this->app = AppFactory::create();

        // Configurer Twig
        $twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);

        // Ajouter le middleware Twig
        $this->app->add(TwigMiddleware::create($this->app, $twig));

        // Définir l'objet Booking
        $this->booking = new Booking(197); // Capacité maximale

        // Définir les routes
        $this->app->get('/', [new BookingController($twig, $this->booking), 'showForm'])->setName('form');
        $this->app->post('/book', [new BookingController($twig, $this->booking), 'bookSeat'])->setName('book');
    }

    public function testShowForm()
    {
        $request = (new ServerRequestFactory)->createServerRequest('GET', '/');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBookSeat()
    {
        $request = (new ServerRequestFactory)->createServerRequest('POST', '/book')
            ->withParsedBody(['seat' => 'A1']);
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Seat A1 successfully booked.', (string) $response->getBody());
    }

    public function testBookSameSeatTwice()
    {
        $request = (new ServerRequestFactory)->createServerRequest('POST', '/book')
            ->withParsedBody(['seat' => 'A1']);
        $this->app->handle($request);

        $request = (new ServerRequestFactory)->createServerRequest('POST', '/book')
            ->withParsedBody(['seat' => 'A1']);
        $response = $this->app->handle($request);

        $this->assertStringContainsString('Seat already booked.', (string) $response->getBody());
    }

    public function testCannotBookMoreThanCapacity()
    {
        for ($i = 1; $i <= 198; $i++) {
            $seat = 'A' . $i;
            $request = (new ServerRequestFactory)->createServerRequest('POST', '/book')
                ->withParsedBody(['seat' => $seat]);
            $response = $this->app->handle($request);

            if ($i > 197) {
                $this->assertStringContainsString('No more seats available.', (string) $response->getBody());
                break;
            }
        }
    }
}
