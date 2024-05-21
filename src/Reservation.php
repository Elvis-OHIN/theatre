<?php
namespace TheatreReservation;

class Reservation
{
    private $configuration;

    public function __construct($configuration)
    {
        if (!in_array($configuration, [127, 163, 197])) {
            throw new \InvalidArgumentException('Invalid configuration');
        }
        $this->configuration = $configuration;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function reserveSeat($row, $seatNumber)
    {
        // Placeholder pour la logique de réservation
        return "Seat {$row}{$seatNumber} reserved.";
    }
}
