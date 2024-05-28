<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class BookingController
{
    private $view;
    private $booking;

    public function __construct(Twig $view, Booking $booking)
    {
        $this->view = $view;
        $this->booking = $booking;
    }

    private function generateSeats()
    {
        $rows = range('A', 'O');
        $seats = [];
        foreach ($rows as $row) {
            for ($i = 1; $i <= 12; $i++) {
                $seats[] = $row . $i;
            }
        }
        return $seats;
    }

    public function showForm(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'form.twig', [
            'seats' => $this->generateSeats(),
            'bookedSeats' => $this->booking->getBookedSeats(),
            'formAction' => '/book'
        ]);
    }

    public function bookSeat(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $seat = $data['seat'];
        try {
            $this->booking->bookSeat($seat);
            $message = "Seat $seat successfully booked.";
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return $this->view->render($response, 'form.twig', [
            'message' => $message,
            'seats' => $this->generateSeats(),
            'bookedSeats' => $this->booking->getBookedSeats(),
            'formAction' => '/book'
        ]);
    }
}