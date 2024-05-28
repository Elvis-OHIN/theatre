<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class BookingController
{
    private $twig;
    private $pdo;

    public function __construct(Twig $twig, PDO $pdo)
    {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function book(Request $request, Response $response): Response
    {
        $stmt = $this->pdo->query("SELECT s.id, s.name, s.description, s.capacity, 
                                   (s.capacity - COALESCE(b.count, 0)) AS availableSeats
                                   FROM spectacles s
                                   LEFT JOIN (SELECT spectacle_id, COUNT(*) AS count 
                                   FROM bookings GROUP BY spectacle_id) b
                                   ON s.id = b.spectacle_id");
        $spectacles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($spectacles as &$spectacle) {
            $stmt = $this->pdo->prepare("SELECT seatNumber, rang FROM bookings WHERE spectacle_id = :spectacle_id");
            $stmt->execute([':spectacle_id' => $spectacle['id']]);
            $spectacle['bookedSeats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->twig->render($response, 'booking.html.twig', ['spectacles' => $spectacles]);
    }

    public function handleBooking(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $stmt = $this->pdo->prepare("SELECT capacity FROM spectacles WHERE id = :spectacle_id");
        $stmt->execute([':spectacle_id' => $data['spectacle_id']]);
        $capacity = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM bookings WHERE spectacle_id = :spectacle_id");
        $stmt->execute([':spectacle_id' => $data['spectacle_id']]);
        $bookedCount = $stmt->fetchColumn();

        if ($bookedCount >= $capacity) {
            return $response->withHeader('Location', '/booking?error=no_seats')->withStatus(302);
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE spectacle_id = :spectacle_id 
        AND seatNumber = :seatNumber AND rang = :rang");
        $stmt->execute([
            ':spectacle_id' => $data['spectacle_id'],
            ':seatNumber' => $data['seatNumber'],
            ':rang' => $data['rang']
        ]);
        $seatCount = $stmt->fetchColumn();

        if ($seatCount > 0) {
            return $response->withHeader('Location', '/booking?error=seat_taken')->withStatus(302);
        }

        $stmt = $this->pdo->prepare("INSERT INTO bookings (spectacle_id, seatNumber, rang) 
            VALUES (:spectacle_id, :seatNumber, :rang)");
        $stmt->execute([
            ':spectacle_id' => $data['spectacle_id'],
            ':seatNumber' => $data['seatNumber'],
            ':rang' => $data['rang']
        ]);

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
