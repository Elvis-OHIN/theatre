<?php

use Slim\App;
use App\Controller\HomeController;
use App\Controller\BookingController;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index']);
    $app->get('/booking', [BookingController::class, 'book']);
    $app->post('/booking', [BookingController::class, 'handleBooking']);
};
