<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . "/../src/Booking.php";
require __DIR__ . "/../src/BookingController.php";

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Booking;
use App\BookingController;

$app = AppFactory::create();

// Configurer Twig
$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);

// Ajouter le middleware Twig
$app->add(TwigMiddleware::create($app, $twig));

// Définir l'objet Booking
$booking = new Booking(197); // Capacité maximale

// Définir les routes avec des noms explicites
$app->get('/', [new BookingController($twig, $booking), 'showForm'])->setName('form');
$app->post('/book', [new BookingController($twig, $booking), 'bookSeat'])->setName('book');

// Lancer l'application
$app->run();
